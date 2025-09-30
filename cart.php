<?php
// cart.php - Trang giỏ hàng Mizuki’s Cakes
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: cart.php");
    exit();
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $img = $_POST['img'];
    $price = $_POST['price'];
    $qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
    $note = isset($_POST['note']) ? $_POST['note'] : '';
    // Nếu sản phẩm đã có thì tăng số lượng, cập nhật ghi chú mới nhất
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]['qty'] += $qty;
        $_SESSION['cart'][$id]['note'] = $note;
    } else {
        $_SESSION['cart'][$id] = [
            'id' => $id,
            'name' => $name,
            'img' => $img,
            'price' => $price,
            'qty' => $qty,
            'note' => $note
        ];
    }
}

// Xóa sản phẩm khỏi giỏ hàng
if (isset($_POST['remove_cart'])) {
    $id = $_POST['id'];
    unset($_SESSION['cart'][$id]);
}

// Tìm kiếm sản phẩm
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Giỏ hàng – Mizuki’s Cakes</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root{
      --brown:#b48d61;
      --brown-700:#5b4632;
      --cream:#f6efe6;
      --dark:#2b2219;
    }
    *{box-sizing:border-box}
    html,body{margin:0;font-family:Poppins,system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#2b2219;background:#fff;scroll-behavior:smooth}
    a{color:inherit;text-decoration:none}
    .container{width:min(1200px, 92%);margin:0 auto}
    .nav{position:sticky;top:0;z-index:50;background:var(--brown);color:#fff;box-shadow:0 2px 10px rgba(0,0,0,.1)}
    .nav-inner{display:flex;align-items:center;justify-content:space-between;padding:10px 0}
    .brand{font-weight:700;letter-spacing:.22em;font-size:18px}
    .menu{display:flex;gap:22px;font-weight:500}
    .menu a{opacity:.95}
    .menu a:hover{opacity:1;text-decoration:underline}
    .cta{background:#fff;color:var(--brown-700);padding:8px 14px;border-radius:999px;font-weight:600}
    .search-box {
      display: flex;
      align-items: center;
      background: #fff;
      border-radius: 999px;
      padding: 2px 8px 2px 12px;
      margin-left: 18px;
      border: 1px solid #e0d3c2;
      height: 36px;
    }
    .search-box input {
      border: none;
      outline: none;
      background: transparent;
      font-size: 15px;
      color: #5b4632;
      width: 110px;
      font-family: inherit;
    }
    .search-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 4px 4px 4px 8px;
      display: flex;
      align-items: center;
    }
    .search-btn svg {
      display: block;
    }
    @media (max-width: 700px) {
      .search-box {
        display: none;
      }
    }
    .login-btn, .register-btn {
      margin-left: 10px;
      padding: 7px 16px;
      border-radius: 999px;
      border: 1px solid var(--brown-700);
      background: #fff;
      color: var(--brown-700);
      font-weight: 600;
      font-family: inherit;
      cursor: pointer;
      transition: background .18s, color .18s;
    }
    .login-btn:hover, .register-btn:hover {
      background: var(--brown-700);
      color: #fff;
    }
    .cart-btn {
      background: #fff;
      border: 1px solid var(--brown-700);
      border-radius: 999px;
      padding: 6px 10px;
      margin-left: 12px;
      margin-right: 12px;
      cursor: pointer;
      display: flex;
      align-items: center;
      transition: background .18s, border-color .18s;
      position: relative;
    }
    .cart-btn:hover {
      background: var(--cream);
      border-color: var(--brown);
    }
    .cart-btn svg {
      display: block;
    }
    .cart-badge {
      position: absolute;
      top: 2px;
      right: 2px;
      background: #e74c3c;
      color: #fff;
      font-size: 13px;
      font-weight: bold;
      border-radius: 50%;
      padding: 2px 7px;
      min-width: 22px;
      text-align: center;
      box-shadow: 0 2px 6px rgba(0,0,0,.15);
    }
    /* Cart page */
    .cart-section{background:#fff7ee;min-height:60vh;padding:56px 0;}
    .cart-title{text-align:center;margin:0 0 32px;font-size:36px;letter-spacing:.16em;color:var(--brown-700)}
    .cart-list{display:flex;flex-direction:column;gap:24px;}
    .cart-item{display:flex;gap:22px;align-items:center;background:#fff;border-radius:14px;box-shadow:0 4px 16px rgba(0,0,0,.07);padding:18px 22px;}
    .cart-item img{width:90px;height:90px;object-fit:cover;border-radius:10px;background:#f6efe6;}
    .cart-item .cart-info{flex:1;}
    .cart-item .cart-name{font-weight:700;font-size:1.1rem;color:#5b4632;}
    .cart-item .cart-size{font-size:14px;color:#888;}
    .cart-item input[type="number"]{width:48px;padding:4px 6px;border-radius:6px;border:1px solid #e0d3c2;margin-left:6px;}
    .cart-item .cart-price{font-weight:700;color:#b48d61;font-size:1.1rem;}
    .cart-item .cart-remove{background:none;border:none;color:#b48d61;font-size:22px;cursor:pointer;margin-left:12px;}
    .cart-summary{display:flex;justify-content:flex-end;align-items:center;gap:32px;margin-top:36px;}
    .cart-summary-total{font-size:1.15rem;font-weight:700;color:#5b4632;}
    .cart-summary-total span{color:#b48d61;font-size:1.3rem;}
    .btn{display:inline-block;background:var(--brown);color:#fff;padding:12px 18px;border-radius:12px;font-weight:600;box-shadow:0 6px 16px rgba(0,0,0,.12);text-align:center;}
    .btn:hover{background:var(--brown-700);}
    /* Footer & phone */
    footer{background:var(--brown);color:#fff;margin-top:40px}
    .foot{display:grid;gap:18px;padding:26px 0}
    @media (min-width: 900px){
      .foot{grid-template-columns:1.2fr .8fr}
    }
    .copy{text-align:center;padding:14px 0;background:var(--brown-700);font-size:14px}
    .phone{position:fixed;right:18px;bottom:18px;z-index:60;background:var(--brown);color:#fff;width:52px;height:52px;border-radius:50%;display:grid;place-items:center;box-shadow:0 10px 24px rgba(0,0,0,.2)}
  </style>
</head>
<body>
  <nav class="nav">
    <div class="container nav-inner">
      <div class="brand">
        <a href="index.php" style="color:inherit;text-decoration:none;">Mizuki’s Cakes</a>
      </div>
      <div class="menu">
        <a href="#uu-dai">Ưu đãi</a>
        <a href="#menu">Menu</a>
        <a href="#ve-chung-toi">Về chúng tôi</a>
        <a class="cta" href="#lien-he">Đặt bánh</a>
        <button class="cart-btn" aria-label="Giỏ hàng">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <circle cx="9" cy="21" r="1.5" fill="#b48d61"/>
            <circle cx="18" cy="21" r="1.5" fill="#b48d61"/>
            <path d="M3 5h2l1.68 10.39A2 2 0 0 0 8.65 17h7.7a2 2 0 0 0 1.97-1.61L21 7H6" stroke="#b48d61" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="cart-badge">2</span>
        </button>
        <div class="search-box">
          <input type="text" placeholder="Tìm kiếm..." />
          <button class="search-btn" aria-label="Tìm kiếm">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <circle cx="9" cy="9" r="7" stroke="#5b4632" stroke-width="2"/>
              <line x1="14.4142" y1="14" x2="18" y2="17.5858" stroke="#5b4632" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </div>
        <?php if (isset($_SESSION['user'])): ?>
          <form method="post" style="display:inline;">
            <button class="login-btn" type="submit" name="logout">Đăng xuất</button>
          </form>
        <?php else: ?>
          <button class="login-btn" onclick="window.location='auth.php'">Đăng nhập</button>
          <button class="register-btn" onclick="window.location='auth.php'">Đăng ký</button>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <section class="cart-section">
    <div class="container">
      <h2 class="cart-title">GIỎ HÀNG</h2>
      <div class="cart-list">
        <?php if (empty($_SESSION['cart'])): ?>
          <div style="text-align:center;color:#b48d61;font-size:1.2rem;">Chưa có sản phẩm nào trong giỏ hàng.</div>
        <?php else: ?>
          <?php foreach ($_SESSION['cart'] as $item): ?>
            <form class="cart-item" method="post">
              <img src="<?php echo htmlspecialchars($item['img']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
              <div class="cart-info">
                <div class="cart-name"><?php echo htmlspecialchars($item['name']); ?></div>
                <div class="cart-size">ID: <?php echo $item['id']; ?></div>
                <?php if (!empty($item['note'])): ?>
                  <div style="color:#b48d61;font-size:15px;margin-top:4px;">Ghi chú: <?php echo htmlspecialchars($item['note']); ?></div>
                <?php endif; ?>
                <div style="margin-top:6px;">
                  <label>Số lượng:
                    <input type="number" name="qty" value="<?php echo $item['qty']; ?>" min="1" onchange="this.form.submit()">
                  </label>
                </div>
              </div>
              <div class="cart-price"><?php echo number_format($item['price'], 0, ',', '.') . '₫'; ?></div>
              <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
              <button class="cart-remove" title="Xóa sản phẩm" type="submit" name="remove_cart">&times;</button>
            </form>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
      <div class="cart-summary">
        <div class="cart-summary-total">
          Tổng cộng: <span>
            <?php
              $total = 0;
              foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['qty'];
              }
              echo number_format($total, 0, ',', '.') . '₫';
            ?>
          </span>
        </div>
        <a href="order.php" class="btn" style="font-size:1.1rem;">Đặt hàng</a>
      </div>
    </div>
  </section>

  <footer id="lien-he">
    <div class="container foot">
      <div>
        <h4 style="margin:0 0 8px">Mizuki’s Cakes</h4>
        <p>20 Ngô Quyền, Hoàn Kiếm, Hà Nội<br/>
           67 Văn Cao, Ba Đình, Hà Nội<br/>
           </p>
        <p>PRE-ORDER: <a href="tel:0325325946">0325325946</a><br/>
           Email: <a href="mail:20221046@eaut.edu.vn">20221046@eaut.edu.vn</a></p>
      </div>
      <div>
        <h4 style="margin:0 0 8px">MẠNG XÃ HỘI</h4>
        <p><a target="_blank">Facebook</a> • <a target="_blank">Instagram</a> • <a target="_blank">TikTok</a></p>
        <p>Giờ mở cửa: 09:00 – 21:00</p>
        <a class="btn" href="#menu">Xem menu</a>
      </div>
    </div>
    <div class="copy">A PIECE OF CAKE, A PART OF WHOLE LIFE – Bánh Sẻ Chia, Trọn Cuộc Sống</div>
  </footer>
  <a class="phone" href="tel:2022104620221062" aria-label="Gọi đặt bánh">☎</a>

  <script>
function updateTotal() {
  let total = 0;
  const cartItems = document.querySelectorAll('.cart-item');
  cartItems.forEach(item => {
    const qty = parseInt(item.querySelector('input[type="number"]').value) || 0;
    const priceText = item.querySelector('.cart-price').getAttribute('data-price');
    const price = parseInt(priceText.replace(/\D/g, ''));
    total += qty * price;
  });
  // Định dạng lại số tiền
  document.querySelector('.cart-summary-total span').textContent = total.toLocaleString('vi-VN') + '₫';
}

// Gán giá trị gốc cho mỗi sản phẩm (chỉ làm 1 lần)
document.querySelectorAll('.cart-item').forEach(item => {
  const priceEl = item.querySelector('.cart-price');
  if (!priceEl.hasAttribute('data-price')) {
    // Lưu giá gốc vào thuộc tính data-price (không nhân với số lượng)
    priceEl.setAttribute('data-price', priceEl.textContent);
  }
  // Lắng nghe sự kiện thay đổi số lượng
  item.querySelector('input[type="number"]').addEventListener('input', updateTotal);
});

// Gọi lần đầu để cập nhật tổng
updateTotal();
</script>
</body>
</html>

<?php
$conn = new mysqli("localhost", "root", "", "mizuki_cakes");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);
$where = $search ? "WHERE name LIKE '%$search%'" : '';
$products = $conn->query("SELECT * FROM products $where ORDER BY id DESC");
?>
<section class="section" id="menu">
  <div class="container">
    <h3 style="margin-bottom:18px;color:#b48d61;">Thêm sản phẩm vào giỏ hàng</h3>
    <div class="cat-grid" style="display:flex;flex-wrap:wrap;gap:24px;">
      <?php while ($row = $products->fetch_assoc()): ?>
        <form method="post" style="background:#fff;border-radius:12px;padding:18px;margin-bottom:18px;box-shadow:0 2px 10px rgba(0,0,0,.07);width:220px;">
          <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>" style="width:100%;height:140px;object-fit:cover;border-radius:10px;">
          <div style="font-weight:700;margin:8px 0;"><?php echo htmlspecialchars($row['name']); ?></div>
          <div style="color:#b48d61;font-size:1.1rem;"><?php echo number_format($row['price'], 0, ',', '.') . '₫'; ?></div>
          <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
          <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
          <input type="hidden" name="img" value="<?php echo htmlspecialchars($row['img']); ?>">
          <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
          <label style="margin:8px 0;display:block;">Số lượng: <input type="number" name="qty" value="1" min="1" style="width:48px;"></label>
          <button type="submit" name="add_to_cart" class="btn" style="width:100%;margin-top:8px;">Thêm vào giỏ hàng</button>
        </form>
      <?php endwhile; ?>
    </div>
  </div>
</section>