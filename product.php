<?php
// product_burnt_caramel_signature.php - BURNT CARAMEL SIGNATURE – Mizuki’s Cakes
session_start();
$conn = new mysqli("localhost", "root", "", "mizuki_cakes");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
    echo "<h2>Không tìm thấy sản phẩm!</h2>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title><?php echo htmlspecialchars($product['name']); ?> – Mizuki’s Cakes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
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
    /* Product detail */
    .product-section{padding:56px 0;background:#fff7ee;}
    .product-detail{display:flex;flex-wrap:wrap;gap:36px;align-items:center;background:#fff;border-radius:18px;box-shadow:0 6px 24px rgba(0,0,0,.08);padding:32px;}
    .product-img{flex:1 1 320px;min-width:260px;}
    .product-img img{width:100%;border-radius:14px;box-shadow:0 4px 16px rgba(0,0,0,.10);background:#f6efe6;}
    .product-info{flex:2 1 340px;min-width:260px;}
    .product-info h2{margin-top:0;color:#b48d61;font-size:2rem;font-weight:700;}
    .badge{display:inline-block;background:var(--cream);color:var(--brown-700);border:1px solid var(--brown);padding:2px 8px;border-radius:999px;font-size:12px;margin-right:6px}
    .product-info p{font-size:1.1rem;line-height:1.7;color:#5b4632;}
    .product-price{font-size:1.3rem;font-weight:800;color:#b48d61;margin:18px 0 22px;}
    .product-form{display:grid;gap:16px;max-width:340px;}
    .product-form input[type="number"]{width:60px;padding:6px 8px;border-radius:8px;border:1px solid #e0d3c2;margin-left:8px;}
    .product-form textarea{width:100%;padding:8px;border-radius:8px;border:1px solid #e0d3c2;}
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
    .product-detail{max-width:700px;margin:40px auto;background:#fff;border-radius:18px;box-shadow:0 8px 24px rgba(0,0,0,.12);padding:32px;display:flex;gap:32px;align-items:flex-start;}
    .product-detail img{width:280px;border-radius:14px;object-fit:cover;}
    .product-info{flex:1;}
    .product-info h1{margin:0 0 12px;font-size:2rem;color:#b48d61;}
    .product-info .price{font-size:1.3rem;color:#d08c3a;font-weight:600;margin-bottom:14px;}
    .product-info p{margin-bottom:18px;}
    .btn{background:#b48d61;color:#fff;padding:10px 18px;border-radius:12px;font-weight:600;text-decoration:none;}
    .btn:hover{background:#5b4632;}
    .note-input{padding:6px;border-radius:8px;border:1px solid #e0d3c2;width:100%;margin-bottom:10px;}
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
        <button class="cart-btn" aria-label="Giỏ hàng" onclick="window.location='cart.php'">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <circle cx="9" cy="21" r="1.5" fill="#b48d61"/>
            <circle cx="18" cy="21" r="1.5" fill="#b48d61"/>
            <path d="M3 5h2l1.68 10.39A2 2 0 0 0 8.65 17h7.7a2 2 0 0 0 1.97-1.61L21 7H6" stroke="#b48d61" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="cart-badge">1</span>
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
        <button class="login-btn" onclick="window.location='auth.php'">Đăng nhập</button>
        <button class="register-btn" onclick="window.location='auth.php'">Đăng ký</button>
      </div>
    </div>
  </nav>

  <section class="product-section">
    <div class="container">
      <div class="product-detail">
        <div class="product-img">
          <img src="<?php echo htmlspecialchars($product['img']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        </div>
        <div class="product-info">
          <h2><?php echo htmlspecialchars($product['name']); ?></h2>
          <div style="margin-bottom:10px;">
            <?php if (!empty($product['category'])): ?>
              <span class="badge"><?php echo htmlspecialchars($product['category']); ?></span>
            <?php endif; ?>
            <?php if (!empty($product['size'])): ?>
              <span class="badge"><?php echo htmlspecialchars($product['size']); ?></span>
            <?php endif; ?>
          </div>
          <p>
            <?php echo nl2br(htmlspecialchars($product['description'])); ?>
          </p>
          <div class="product-price"><?php echo number_format($product['price'], 0, ',', '.') . '₫'; ?></div>
          <form class="product-form" method="post" action="cart.php">
            <label>
              Số lượng:
              <input type="number" name="qty" min="1" value="1">
            </label>
            <label>
              Ghi chú cho cửa hàng:
              <textarea name="note" rows="2"></textarea>
            </label>
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
            <input type="hidden" name="img" value="<?php echo htmlspecialchars($product['img']); ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            <button type="submit" name="add_to_cart" class="btn">Thêm vào giỏ hàng</button>
          </form>
          <form class="product-form" method="post" action="order.php" style="margin-top:10px;">
            <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
            <input type="hidden" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">
            <input type="hidden" name="img" value="<?php echo htmlspecialchars($product['img']); ?>">
            <input type="hidden" name="price" value="<?php echo $product['price']; ?>">
            <input type="hidden" name="qty" value="1">
            <input type="text" name="note" class="note-input" placeholder="Ghi chú (tuỳ chọn)">
            <button type="submit" class="btn">Đặt ngay</button>
          </form>
          <div style="margin-top:18px;font-size:14px;color:#888;">
            <span>☎ Hỗ trợ đặt hàng: <a href="tel:0325325946" style="color:#b48d61;">0325325946</a></span>
          </div>
        </div>
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
</body>
</html>