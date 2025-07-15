<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <title>Thông tin tài khoản</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .profile-container {
            max-width: 480px;
            margin: 40px auto;
            background: #fff;
            padding: 30px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        h1 {
            font-weight: 600;
            color: #343a40;
            margin-bottom: 25px;
            text-align: center;
        }
        label {
            font-weight: 500;
            color: #495057;
        }
        input.form-control {
            border-radius: 6px;
            border: 1px solid #ced4da;
            transition: border-color 0.3s ease;
        }
        input.form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 5px rgba(13, 110, 253, 0.5);
        }
        hr {
            margin: 30px 0;
            border-color: #dee2e6;
        }
        .btn-primary {
            background-color: #0d6efd;
            border: none;
            font-weight: 600;
            padding: 10px 0;
            border-radius: 6px;
            transition: background-color 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #0b5ed7;
        }
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            padding: 10px 0;
            border-radius: 6px;
            font-weight: 500;
        }
        .btn-secondary:hover {
            background-color: #5c636a;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h1>Thông tin tài khoản</h1>
        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php elseif (isset($success) && $success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Tên đăng nhập</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= htmlspecialchars($user['username']) ?>" readonly />
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" />
            </div>
            <hr />
            <h5>Thay đổi mật khẩu</h5>
            <div class="mb-3">
                <label for="password" class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" class="form-control" id="password" name="password" />
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" />
            </div>
            <button type="submit" class="btn btn-primary w-100">Lưu thay đổi</button>
            <a href="/buoi2/Product/index" class="btn btn-secondary mt-3 w-100">Quay lại trang sản phẩm</a>
        </form>
    </div>
</body>
</html>
