<?php include 'app/views/shares/header.php'; ?>

<style>
    body {
        background-color: #ffffff;
        color: #212529;
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }
    .container {
        max-width: 1200px;
        margin: 40px auto;
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    h1 {
        color: #0d6efd;
        font-weight: 700;
        margin-bottom: 30px;
        text-align: center;
    }
    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 10px;
        font-size: 15px;
    }
    thead tr {
        background-color: #0d6efd;
        color: white;
        border-radius: 10px;
    }
    thead th {
        padding: 15px 10px;
        text-align: left;
        border: none;
    }
    tbody tr {
        background-color: #ffffff;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        border-radius: 10px;
        transition: transform 0.2s ease;
    }
    tbody tr:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    tbody td {
        padding: 15px 10px;
        vertical-align: middle;
        border: none;
    }
    button.btn-primary, button.btn-danger, button.btn-success {
        padding: 6px 12px;
        font-size: 14px;
        border-radius: 5px;
    }
    button.btn-primary {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
    }
    button.btn-primary:hover {
        background-color: #084298;
        border-color: #084298;
    }
    button.btn-danger {
        background-color: #dc3545;
        border-color: #dc3545;
        color: white;
    }
    button.btn-danger:hover {
        background-color: #bb2d3b;
        border-color: #b02a37;
    }
    button.btn-success {
        background-color: #198754;
        border-color: #198754;
        color: white;
    }
    button.btn-success:hover {
        background-color: #146c43;
        border-color: #146c43;
    }
</style>

<div class="container">
    <h1>Quản lý danh mục</h1>
    <?php if (!empty($categories)): ?>
        <table>
            <thead>
                <tr>
                    <th>Tên danh mục</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm btn-edit-category" data-id="<?= $category->id ?>" data-name="<?= htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') ?>" data-description="<?= htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8') ?>">Sửa</button>
                            <a href="/buoi2/Category/delete/<?= $category->id ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center">Chưa có danh mục nào.</p>
    <?php endif; ?>

    <button id="show-add-category-btn" class="btn btn-success mt-3">Thêm danh mục mới</button>

    <form id="add-category-form" action="/buoi2/Category/save" method="POST" style="display:none; margin-top: 15px;">
        <div class="mb-2">
            <input type="text" name="name" class="form-control" placeholder="Tên danh mục" required>
        </div>
        <div class="mb-2">
            <textarea name="description" class="form-control" placeholder="Mô tả"></textarea>
        </div>
        <button type="submit" class="btn btn-success btn-sm">Thêm</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var showAddBtn = document.getElementById('show-add-category-btn');
    var addForm = document.getElementById('add-category-form');
    showAddBtn.addEventListener('click', function() {
        if (addForm.style.display === 'none') {
            addForm.style.display = 'block';
            showAddBtn.textContent = 'Ẩn form thêm danh mục';
        } else {
            addForm.style.display = 'none';
            showAddBtn.textContent = 'Thêm danh mục mới';
        }
    });
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
