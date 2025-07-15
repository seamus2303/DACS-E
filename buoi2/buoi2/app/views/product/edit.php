<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: /buoi2/User/login');
    exit();
}
include 'app/views/shares/header.php'; 
?>
<div class="container mt-5">
    <h1 class="text-center mb-4">Sửa sản phẩm</h1>

    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="POST" action="/buoi2/Product/update" onsubmit="return validateForm();" class="shadow p-4 rounded bg-light" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo isset($product->id) ? $product->id : ''; ?>">
        <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($product->image ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group mb-3">
            <label for="name" class="form-label">Tên sản phẩm:</label>
            <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($product->name) ? htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="description" class="form-label">Mô tả:</label>
            <textarea id="description" name="description" class="form-control" rows="4" required><?php echo isset($product->description) ? htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8') : ''; ?></textarea>
        </div>

        <div class="form-group mb-3">
            <label for="image" class="form-label">Hình ảnh:</label>
            <input type="file" id="image" name="image" class="form-control" accept="image/*">
            <?php if (!empty($product->image)): ?>
                <img src="/buoi2/<?php echo htmlspecialchars($product->image); ?>" alt="Hình ảnh sản phẩm" style="max-width: 150px; margin-top: 10px;">
            <?php endif; ?>
        </div>

        <div class="form-group mb-3">
            <label for="price" class="form-label">Giá:</label>
            <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo isset($product->price) ? htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8') : ''; ?>" required>
        </div>

        <div class="form-group mb-3">
            <label for="category_id" class="form-label">Danh mục:</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">-- Chọn danh mục --</option>
                <?php if (!empty($categories)): ?>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category->id; ?>" <?php echo isset($product->category_id) && $category->id == $product->category_id ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            <a href="/buoi2/Product/list" class="btn btn-secondary">Quay lại danh sách sản phẩm</a>
        </div>
    </form>
</div>
<?php include 'app/views/shares/footer.php'; ?>
