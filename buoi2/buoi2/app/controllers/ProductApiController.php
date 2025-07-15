<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductApiController
{
    private $productModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // Lấy danh sách sản phẩm
    public function index()
    {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }


    // Thêm sản phẩm mới, nhận dữ liệu hình ảnh base64 trong JSON
    public function store()
    {
        header('Content-Type: application/json');
        try {
            $input = json_decode(file_get_contents('php://input'), true);

            $name = $input['name'] ?? '';
            $description = $input['description'] ?? '';
            $price = $input['price'] ?? '';
            $category_id = $input['category_id'] ?? null;
            $imageBase64 = $input['image'] ?? '';

            $image = '';
            if ($imageBase64) {
                $image = $this->saveBase64Image($imageBase64);
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

            if (is_array($result)) {
                http_response_code(400);
                echo json_encode(['errors' => $result]);
            } else {
                http_response_code(201);
                echo json_encode(['message' => 'Product created successfully']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Cập nhật sản phẩm theo ID, nhận tên file ảnh trong JSON
    public function update($id)
    {
        header('Content-Type: application/json');
        try {
            parse_str(file_get_contents("php://input"), $put_vars);
            $name = $put_vars['name'] ?? '';
            $description = $put_vars['description'] ?? '';
            $price = $put_vars['price'] ?? '';
            $category_id = $put_vars['category_id'] ?? null;
            $image = $put_vars['image'] ?? $put_vars['existing_image'] ?? '';

            $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);

            if ($result) {
                echo json_encode(['message' => 'Product updated successfully']);
            } else {
                http_response_code(400);
                echo json_encode(['message' => 'Product update failed']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }

    private function uploadImage($file)
    {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return $target_file;
    }

    private function uploadImageFile($file)
  {
      return $this->uploadImage($file);
   }
    private function saveBase64Image($base64Image)
    {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        // Extract base64 string without metadata prefix
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $imageType = strtolower($type[1]); // jpg, png, gif, etc.
        } else {
            throw new Exception("Dữ liệu hình ảnh không hợp lệ.");
        }
        $base64Image = str_replace(' ', '+', $base64Image);
        $imageData = base64_decode($base64Image);
        if ($imageData === false) {
            throw new Exception("Không thể giải mã dữ liệu hình ảnh.");
        }
        $fileName = uniqid() . '.' . $imageType;
        $filePath = $target_dir . $fileName;
        if (file_put_contents($filePath, $imageData) === false) {
            throw new Exception("Không thể lưu hình ảnh.");
        }
        return $filePath;
    }
}
?>
