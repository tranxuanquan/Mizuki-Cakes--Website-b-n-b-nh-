<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $img = $_POST['img'];
    $price = $_POST['price'];
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    $note = isset($_POST['note']) ? $_POST['note'] : '';
} else {
    // Nếu truy cập trực tiếp không qua form
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Đặt ngay thành công – Mizuki’s Cakes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:Poppins,sans-serif;background:#fff7ee;color:#5b4632;}
    .order-success{max-width:480px;margin:60px auto;background:#fff;border-radius:18px;box-shadow:0 8px 24px rgba(0,0,0,.12);padding:32px;}
    .order-success img{width:100%;max-width:180px;border-radius:12px;margin-bottom:18px;}
    .order-success h2{color:#b48d61;margin-bottom:12px;}
    .order-success .note{background:#f6efe6;padding:10px;border-radius:8px;margin-top:12px;}
    .btn{display:inline-block;background:#b48d61;color:#fff;padding:10px 18px;border-radius:12px;font-weight:600;margin-top:18px;text-decoration:none;}
    .btn:hover{background:#5b4632;}
  </style>
</head>
<body>
  <div class="order-success">
    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($name); ?>">
    <h2>Đặt hàng thành công!</h2>
    <div><strong>Sản phẩm:</strong> <?php echo htmlspecialchars($name); ?></div>
    <div><strong>Số lượng:</strong> <?php echo $qty; ?></div>
    <div><strong>Giá:</strong> <?php echo number_format($price, 0, ',', '.') . '₫'; ?></div>
    <?php if (!empty($note)): ?>
      <div class="note"><strong>Ghi chú của bạn:</strong> <?php echo htmlspecialchars($note); ?></div>
    <?php endif; ?>
    <a href="index.php" class="btn">Quay lại trang chủ</a>
  </div>
</body>
</html>