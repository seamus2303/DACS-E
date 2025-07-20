<?php
// Trang thông tin khách hàng
// Hiển thị: tên khách hàng, số điện thoại, số lần mua, tổng tiền
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin khách hàng</title>
    <link rel="stylesheet" href="/buoi2/app/views/shares/style.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f7f7f7; }
        .customer-container { max-width: 600px; margin: 40px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); padding: 32px; }
        h2 { text-align: center; color: #2c3e50; }
        .customer-info { margin-top: 24px; }
        .customer-info label { font-weight: bold; display: block; margin-bottom: 8px; }
        .customer-info span { display: block; margin-bottom: 16px; }
        .search-form { margin-bottom: 24px; text-align: center; }
        .search-form input[type="text"] { padding: 8px; width: 220px; border-radius: 4px; border: 1px solid #ccc; }
        .search-form button { padding: 8px 16px; border-radius: 4px; background: #3498db; color: #fff; border: none; cursor: pointer; }
        .search-form button:hover { background: #2980b9; }
    </style>
</head>
<body>
    <div class="customer-container">
        <h2>Tra cứu thông tin khách hàng</h2>
        <form class="search-form" method="GET" action="">
            <input type="text" name="search" placeholder="Nhập tên hoặc số điện thoại..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            <button type="submit">Tìm kiếm</button>
        </form>
        <div class="customer-info">
            <?php
            // Giả lập dữ liệu khách hàng
            $customers = [
                ["name" => "Nguyễn Văn A", "phone" => "0901234567", "address" => "123 Lê Lợi, Q.1, TP.HCM", "purchase_count" => 5, "total_amount" => 1500000],
                ["name" => "Trần Thị B", "phone" => "0912345678", "address" => "45 Nguyễn Trãi, Q.5, TP.HCM", "purchase_count" => 2, "total_amount" => 500000],
                ["name" => "Lê Văn C", "phone" => "0987654321", "address" => "78 Trần Hưng Đạo, Q.1, TP.HCM", "purchase_count" => 8, "total_amount" => 3200000],
            ];
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $found = false;
            foreach ($customers as $customer) {
                if ($search === '' || stripos($customer['name'], $search) !== false || stripos($customer['phone'], $search) !== false) {
                    echo '<label>Tên khách hàng:</label><span>' . htmlspecialchars($customer['name']) . '</span>';
                    echo '<label>Số điện thoại:</label><span>' . htmlspecialchars($customer['phone']) . '</span>';
                    echo '<label>Địa chỉ:</label><span>' . htmlspecialchars($customer['address']) . '</span>';
                    echo '<label>Số lần mua:</label><span>' . htmlspecialchars($customer['purchase_count']) . '</span>';
                    echo '<label>Tổng tiền đã mua:</label><span>' . number_format($customer['total_amount'], 0, ",", ".") . ' VNĐ</span>';
                    echo '<hr>';
                    $found = true;
                }
            }
            if (!$found) {
                echo '<p style="text-align:center;color:#e74c3c;">Không tìm thấy khách hàng phù hợp.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>
