<?php
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';
require_once 'app/models/OrderModel.php';

require_once 'app/models/CustomerModel.php';

class CartController {
    private $conn;
    private $productModel;
    private $orderModel;
    private $customerModel;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->productModel = new ProductModel($this->conn);


        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (!isset($_SESSION['selected_indexes'])) {
            $_SESSION['selected_indexes'] = [];
        }
    }

    

    // Trang giỏ hàng
    public function index() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /buoi2/User/login");
            exit;
        }
        $cart = $_SESSION['cart'];
        $cartItems = [];
        if (!empty($cart)) {
            // Lấy tất cả các key trong giỏ hàng (đã là optionKey)
            $optionKeys = array_keys($cart);
            $productIds = [];
            foreach ($cart as $key => $item) {
                $productIds[] = $item['product_id'];
            }
            $products = $this->productModel->getProductsByIds($productIds);
            // Tạo map product_id => product object
            $productMap = [];
            foreach ($products as $product) {
                $productMap[$product['id']] = $product;
            }
            // Kết hợp dữ liệu sản phẩm với tùy chọn trong giỏ hàng
            foreach ($cart as $key => $item) {
                if (isset($productMap[$item['product_id']])) {
                    $product = $productMap[$item['product_id']];
                    $product['quantity'] = $item['quantity'] ?? 0;
                    $product['sugar_level'] = $item['sugar_level'] ?? null;
                    $product['ice_level'] = $item['ice_level'] ?? null;
                    $product['cup_size'] = $item['cup_size'] ?? null;
                    // Tính giá cuối cùng đã cộng thêm 5000 nếu ly lớn
                    $product['final_price'] = $product['price'];
                    if (!empty($item['cup_size']) && $item['cup_size'] === 'Ly lớn') {
                        $product['final_price'] += 5000;
                    }
                    $cartItems[] = $product;
                }
            }
        }
        $selectedIndexes = $_SESSION['selected_indexes'] ?? [];
        require_once 'app/views/Cart/cart.php';
    }

    // Thêm sản phẩm vào giỏ hàng, bao gồm tùy chọn
    public function add() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /buoi2/User/login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $productId = $_POST['product_id'];
            $quantity = (int)($_POST['quantity'] ?? 1);

            // Lấy các tùy chọn nếu có, chuyển mảng thành chuỗi nếu cần
            $sugarLevel = isset($_POST['sugar_level']) ? (is_array($_POST['sugar_level']) ? implode(',', $_POST['sugar_level']) : $_POST['sugar_level']) : null;
            $iceLevel = isset($_POST['ice_level']) ? (is_array($_POST['ice_level']) ? implode(',', $_POST['ice_level']) : $_POST['ice_level']) : null;
            $cupSize = isset($_POST['cup_size']) ? (is_array($_POST['cup_size']) ? implode(',', $_POST['cup_size']) : $_POST['cup_size']) : null;

            // Tạo key duy nhất cho sản phẩm dựa trên id và tùy chọn để phân biệt các sản phẩm cùng id nhưng khác tùy chọn
            $optionKey = md5(json_encode([
                'product_id' => $productId,
                'sugar_level' => $sugarLevel,
                'ice_level' => $iceLevel,
                'cup_size' => $cupSize
            ]));

            if (!isset($_SESSION['cart'][$optionKey])) {
                $_SESSION['cart'][$optionKey] = [
                    'product_id' => $productId,
                    'quantity' => 0,
                    'sugar_level' => $sugarLevel,
                    'ice_level' => $iceLevel,
                    'cup_size' => $cupSize,
                    'price' => 0
                ];
            }
            $_SESSION['cart'][$optionKey]['quantity'] += $quantity;

            // Lấy giá sản phẩm gốc từ database để lưu vào session
            $product = $this->productModel->getProductById($productId);
            if ($product) {
                $_SESSION['cart'][$optionKey]['price'] = $product['price'];
            }

            header("Location: /buoi2/Cart");
            exit;
        }
    }

    // Cập nhật giỏ hàng: số lượng, xóa sản phẩm, lưu trạng thái chọn
    public function update() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /buoi2/User/login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['selected_indexes']) && is_array($_POST['selected_indexes'])) {
                $_SESSION['selected_indexes'] = $_POST['selected_indexes'];
            } else {
                $_SESSION['selected_indexes'] = [];
            }
            if (isset($_POST['delete_index'])) {
                $deleteIndex = $_POST['delete_index'];
                if (isset($_SESSION['cart'])) {
                    $keys = array_keys($_SESSION['cart']);
                    if (isset($keys[$deleteIndex])) {
                        unset($_SESSION['cart'][$keys[$deleteIndex]]);
                    }
                }
            }
            if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
                $keys = array_keys($_SESSION['cart']);
                foreach ($_POST['quantities'] as $index => $quantity) {
                    if (isset($keys[$index]) && $quantity > 0) {
                        $_SESSION['cart'][$keys[$index]]['quantity'] = (int)$quantity;
                    }
                }
            }
            header("Location: /buoi2/Cart");
            exit;
        }
    }

    // Thanh toán
    public function checkout() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /buoi2/User/login");
            exit;
        }

        $cart = $_SESSION['cart'] ?? [];
        $cartItems = [];
        if (!empty($cart)) {
            $productIds = [];
            foreach ($cart as $key => $item) {
                $productIds[] = $item['product_id'];
            }
            $products = $this->productModel->getProductsByIds($productIds);
            $productMap = [];
            foreach ($products as $product) {
                $productMap[$product['id']] = $product;
            }
            
            foreach ($cart as $key => $item) {
                if (isset($productMap[$item['product_id']])) {
                    $product = $productMap[$item['product_id']];
                    $product['quantity'] = $item['quantity'] ?? 0;
                    $product['sugar_level'] = $item['sugar_level'] ?? null;
                    $product['ice_level'] = $item['ice_level'] ?? null;
                    $product['cup_size'] = $item['cup_size'] ?? null;
                    $product['final_price'] = $product['price'];
                    if (!empty($item['cup_size']) && $item['cup_size'] === 'Ly lớn') {
                        $product['final_price'] += 5000;
                    }
                    $cartItems[] = $product;
                }
            }
        }

        $totalAmount = 0;
        foreach ($cartItems as $product) {
            $totalAmount += $product['final_price'] * $product['quantity'];
        }

        extract(['cartItems' => $cartItems, 'totalAmount' => $totalAmount]);
        require_once 'app/views/Cart/checkout.php';
    }

    // Trang thanh toán thành công
    public function success() {
        require_once 'app/views/Cart/success.php';
    }

    
    // Xử lý thanh toán và chuyển hướng
    public function process_payment() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: /buoi2/User/login");
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $paymentMethod = $_POST['payment_method'] ?? '';
            $customerName = $_POST['customer_name'] ?? null;
            $phone = $_POST['phone'] ?? null;
            
            error_log("Received customerName: " . $customerName);
            error_log("Received phone: " . $phone);
            
            // Lấy toàn bộ sản phẩm trong giỏ hàng
            $cart = $_SESSION['cart'] ?? [];
            $cartItems = [];
            if (!empty($cart)) {
                $productIds = [];
                foreach ($cart as $key => $item) {
                    $productIds[] = $item['product_id'];
                }
                $products = $this->productModel->getProductsByIds($productIds);
                $productMap = [];
                foreach ($products as $product) {
                    $productMap[$product['id']] = $product;
                }
                
                foreach ($cart as $key => $item) {
                    if (isset($productMap[$item['product_id']])) {
                        $product = $productMap[$item['product_id']];
                        $product['product_id'] = $item['product_id']; // Đảm bảo có product_id
                        $product['quantity'] = $item['quantity'] ?? 0;
                        $product['sugar_level'] = $item['sugar_level'] ?? null;
                        $product['ice_level'] = $item['ice_level'] ?? null;
                        $product['cup_size'] = $item['cup_size'] ?? null;
                        $cartItems[] = $product;
                    }
                }
            }
            
            $totalAmount = 0;
            foreach ($cartItems as $product) {
                $price = $product['price'];
                if (!empty($product['cup_size']) && $product['cup_size'] === 'Ly lớn') {
                    $price += 5000; // Cộng thêm 5000 nếu là ly lớn
                }
                $totalAmount += $price * $product['quantity'];
            }
            
            error_log("Selected Items: " . print_r($cartItems, true));
            
            // Lấy thông tin user
            $user_id = $_SESSION['user_id'];
            
            // Chuyển đổi dữ liệu cho OrderModel
            $productsForOrder = array_map(function($item) {
                $price = $item['price'];
                if (!empty($item['cup_size']) && $item['cup_size'] === 'Ly lớn') {
                    $price += 5000; // Cộng thêm 5000 nếu là ly lớn
                }
                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'sugar_level' => $item['sugar_level'] ?? null,
                    'ice_level' => $item['ice_level'] ?? null,
                    'cup_size' => $item['cup_size'] ?? null,
                    'price' => $price
                ];
            }, $cartItems);
            
            error_log("Calling addOrderWithDetails with products: " . print_r($productsForOrder, true));
            
            // Lưu đơn hàng với thông tin đầy đủ
            $order_id = $this->orderModel->addOrderWithDetails(
                $user_id,
                $paymentMethod,
                $productsForOrder,
                $customerName,
                $phone
            );

            
            if ($order_id) {
                error_log("Order saved successfully with ID: " . $order_id);
                
                // Xóa giỏ hàng sau khi đặt hàng thành công
                $_SESSION['cart'] = [];
                $_SESSION['selected_indexes'] = [];
                
                $_SESSION['totalAmount'] = $totalAmount;
                $_SESSION['order_id'] = $order_id;
                
                // Redirect theo phương thức thanh toán
                if ($paymentMethod === 'cod') {
                    header("Location: /buoi2/Cart/success");
                } elseif ($paymentMethod === 'bank' || $paymentMethod === 'zalopay') {
                    header("Location: /buoi2/Cart/success");
                } else {
                    header("Location: /buoi2/Cart/checkout");
                }
                exit;
            } else {
                error_log("Failed to save order");
                header("Location: /buoi2/Cart/checkout");
                exit;
            }
        } else {
            header("Location: /buoi2/Cart/checkout");
            exit;
        }
    }
}