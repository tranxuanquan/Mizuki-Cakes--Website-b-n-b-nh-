<?php
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php"); // Quay lại menu chính
    exit();
}
$conn = new mysqli("localhost", "root", "", "mizuki_cakes");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

// Xử lý tìm kiếm sản phẩm theo tên
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search !== '') {
    $stmt = $conn->prepare("SELECT * FROM products WHERE name LIKE ? ORDER BY id DESC");
    $search_param = '%' . $search . '%';
    $stmt->bind_param("s", $search_param);
    $stmt->execute();
    $products = $stmt->get_result();
    $stmt->close();
} else {
    $products = $conn->query("SELECT * FROM products ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Mizuki’s Cakes  – Bánh ngọt</title>
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
    /* Navbar */
    .nav{position:sticky;top:0;z-index:50;background:var(--brown);color:#fff;box-shadow:0 2px 10px rgba(0,0,0,.1)}
    .nav-inner{display:flex;align-items:center;justify-content:space-between;padding:10px 0}
    .brand{font-weight:700;letter-spacing:.22em;font-size:18px}
    .menu{display:flex;gap:22px;font-weight:500}
    .menu a{opacity:.95}
    .menu a:hover{opacity:1;text-decoration:underline}
    .cta{background:#fff;color:var(--brown-700);padding:8px 14px;border-radius:999px;font-weight:600}

    /* Hero */
    .hero{position:relative;background:var(--cream)}
    .hero-track{position:relative;overflow:hidden;border-bottom:8px solid var(--brown)}
    .slide{min-height:64vh;display:flex;align-items:center;justify-content:center;position:relative}
    .slide::before{content:"";position:absolute;inset:0;background:linear-gradient(180deg, rgba(246,239,230,0) 0%, rgba(246,239,230,.8) 80%, rgba(246,239,230,1) 100%);}
    .slide img{width:100%;height:100%;object-fit:cover;display:block;filter:saturate(1.02)}
    .hero-copy{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);text-align:center}
    .hero h1{font-size:clamp(28px,4.5vw,52px);margin:0 0 10px;font-weight:700;letter-spacing:.03em}
    .hero p{margin:0 0 18px;opacity:.9}
    .btn{display:inline-block;background:var(--brown);color:#fff;padding:12px 18px;border-radius:12px;font-weight:600;box-shadow:0 6px 16px rgba(0,0,0,.12)}
    .dots{position:absolute;right:14px;bottom:12px;display:flex;gap:8px}
    .dot{width:9px;height:9px;border-radius:50%;background:#fff;opacity:.6;cursor:pointer;border:1px solid rgba(0,0,0,.12)}
    .dot.active{opacity:1}

    /* Category cards */
    .cats{background:#ead6bb;padding:32px 0}
    .cat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px}
    .card{position:relative;border-radius:22px;overflow:hidden;background:#fff;box-shadow:0 8px 24px rgba(0,0,0,.12)}
    .card img{width:100%;height:220px;object-fit:cover;display:block}
    .card h3{position:absolute;left:16px;bottom:12px;background:rgba(0,0,0,.6);color:#fff;padding:8px 12px;border-radius:10px;margin:0;font-size:18px;letter-spacing:.04em}

    /* Menu */
    .section{padding:56px 0}
    .title{text-align:center;margin:0 0 24px;font-size:36px;letter-spacing:.16em;color:var(--brown-700)}
    .menu-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 18px;
    }
    .item {
      background: #fff;
      border-radius: 16px;
      overflow: hidden;
      border: 1px solid #eee;
      box-shadow: 0 6px 16px rgba(0,0,0,.08);
      display: flex;
      flex-direction: column;
      height: 100%;
    }
    .item .thumb {
      height: 180px;
      overflow: hidden;
    }
    .item .thumb img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      display: block;
    }
    .item .info {
      padding: 14px 16px;
      flex: 1 1 auto;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .item .info p {
      display: -webkit-box;
      -webkit-line-clamp: 3;
      -webkit-box-orient: vertical;
      overflow: hidden;
      min-height: 54px;
    }
    .item .price {
      margin-top: 10px;
    }
    .item .buy, .item form {
      margin-top: 10px;
    }
    .item .info form {
      min-width:180px;
      max-width:220px;
      display:flex;
      flex-direction:column;
      gap:8px;
    }
    .item .info input[type="text"] {
      padding:6px;
      border-radius:8px;
      border:1px solid #e0d3c2;
      font-size:15px;
    }

    /* Banners */
    .banners{display:grid;grid-template-columns:1fr;gap:18px}
    @media (min-width: 900px){
      .banners{grid-template-columns:1fr 1fr}
    }
    .banner{border-radius:18px;overflow:hidden;border:1px solid #eee;box-shadow:0 8px 22px rgba(0,0,0,.1)}
    .banner img{width:100%;height:100%;object-fit:cover;display:block}

    /* About */
    .about{display:grid;gap:28px;align-items:center}
    @media (min-width: 980px){
      .about{grid-template-columns:1fr 1fr}
    }
    .about img{width:100%;border-radius:18px;box-shadow:0 8px 24px rgba(0,0,0,.12)}
    .about h3{margin:0 0 10px;font-size:28px;color:var(--brown-700)}
    .about p{line-height:1.7;opacity:.9}

    /* Footer */
    footer{background:var(--brown);color:#fff;margin-top:40px}
    .foot{display:grid;gap:18px;padding:26px 0}
    @media (min-width: 900px){
      .foot{grid-template-columns:1.2fr .8fr}
    }
    .copy{text-align:center;padding:14px 0;background:var(--brown-700);font-size:14px}

    /* Floating phone bubble */
    .phone{position:fixed;right:18px;bottom:18px;z-index:60;background:var(--brown);color:#fff;width:52px;height:52px;border-radius:50%;display:grid;place-items:center;box-shadow:0 10px 24px rgba(0,0,0,.2)}

    /* Search box */
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

    /* Nút đăng nhập/đăng ký */
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
          <span class="cart-badge">3</span>
        </button>
        <form class="search-box" method="get" action="#menu">
          <input type="text" name="search" placeholder="Tìm kiếm sản phẩm..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
          <button class="search-btn" aria-label="Tìm kiếm">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
              <circle cx="9" cy="9" r="7" stroke="#5b4632" stroke-width="2"/>
              <line x1="14.4142" y1="14" x2="18" y2="17.5858" stroke="#5b4632" stroke-width="2" stroke-linecap="round"/>
            </svg>
          </button>
        </form>
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

  <header class="hero" id="top">
      <div class="hero-track">
        <div class="slide"><img alt="Hero" src="images/slideshow_1.png" loading="lazy"></div>
      </div>
      <div class="hero-copy">
        <h1>Cake & Matcha – Mùa Hè Ngon Lành</h1>
        <p>Bánh Entremet cảm hứng Pháp, kết hợp hương vị trái cây nhiệt đới Việt Nam.</p>
        <a class="btn" href="#menu">Khám phá menu</a>
      </div>
      <div class="dots"><span class="dot active"></span></div>
  </header>

  <section class="cats" id="uu-dai">
    <div class="container">
      <div class="cat-grid">
        <article class="card">
          <img src="images/banner_img1.jpg" alt="Bánh sinh nhật">
          <h3>BÁNH SINH NHẬT</h3>
        </article>
        <article class="card">
          <img src="images/banner_img2.jpg" alt="Bánh lẻ">
          <h3>BÁNH LẺ</h3>
        </article>
        <article class="card">
          <img src="images/banner_img3.jpg" alt="Bánh theo set">
          <h3>GIFTSET</h3>
        </article>
      </div>
    </div>
</section>

  <section class="section" id="menu">
    <div class="container">
      <h2 class="title">MENU</h2>
      <div class="menu-grid">
        <!-- Chỉ giữ sản phẩm từ database -->
        <?php while ($row = $products->fetch_assoc()): ?>
          <article class="item">
            <div class="thumb">
              <a href="product.php?id=<?php echo $row['id']; ?>">
                <img src="<?php echo htmlspecialchars($row['img']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
              </a>
            </div>
            <div class="info">
              <div class="name"><?php echo htmlspecialchars($row['name']); ?></div>
              <p><?php echo htmlspecialchars($row['description']); ?></p>
              <div class="price"><?php echo number_format($row['price'], 0, ',', '.') . '₫'; ?></div>
              <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <!-- Đặt ngay -->
                <form method="post" action="order.php" style="flex:1;min-width:180px;max-width:220px;display:flex;flex-direction:column;gap:8px;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                  <input type="hidden" name="img" value="<?php echo htmlspecialchars($row['img']); ?>">
                  <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                  <input type="hidden" name="qty" value="1">
                  <input type="text" name="note" placeholder="Ghi chú (tuỳ chọn)" style="padding:6px;border-radius:8px;border:1px solid #e0d3c2;">
                  <button type="submit" class="btn" style="width:100%;">Đặt ngay</button>
                </form>
                <!-- Giỏ hàng -->
                <form method="post" action="cart.php" style="flex:1;min-width:180px;max-width:220px;display:flex;flex-direction:column;gap:8px;">
                  <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                  <input type="hidden" name="name" value="<?php echo htmlspecialchars($row['name']); ?>">
                  <input type="hidden" name="img" value="<?php echo htmlspecialchars($row['img']); ?>">
                  <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
                  <input type="hidden" name="qty" value="1">
                  <input type="text" name="note" placeholder="Ghi chú (tuỳ chọn)" style="padding:6px;border-radius:8px;border:1px solid #e0d3c2;">
                  <button type="submit" name="add_to_cart" class="btn" style="width:100%;">Giỏ hàng</button>
                </form>
              </div>
            </div>
          </article>
        <?php endwhile; ?>
      </div>
    </div>
  </section>
  <section class="section" id="uu-dai-2">
    <div class="container">
      <div class="banners">
        <div class="banner"><img src="images/hb_img3.png" alt="Banner 1"></div>
        <div class="banner"><img src="images/hb_img4.png" alt="Banner 2"></div>
      </div>
    </div>
  </section>

  <section class="section" id="ve-chung-toi" style="background:#fff7ee">
    <div class="container about">
      <img src="images/18deeb16-2376-4364-8ab6-a6dda02aeeca.png" alt="About Us">
      <div>
        <h3>A PIECE OF CAKE, A PART OF WHOLE LIFE</h3>
        <p>Artemis Pastry ra đời với niềm đam mê mang đến những chiếc bánh Entremet cấu kỳ
        của ẩm thực Pháp, kết hợp tinh tế hương vị nhiệt đới Việt Nam. Chúng tôi trân trọng trải nghiệm vị giác tinh tế và độc đáo.</p>
        <p>Như một biểu tượng của sự gắn kết, mỗi miếng bánh là một phần của ký ức.
        Cùng sẻ chia và lưu giữ khoảnh khắc đáng nhớ trong cuộc sống.</p>
        <a class="btn" href="#lien-he">Đặt bánh ngay</a>
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
