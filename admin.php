<?php
// admin.php - Trang quản trị sản phẩm Mizuki’s Cakes
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php"); // Quay lại trang chủ sau khi đăng xuất
    exit();
}

$conn = new mysqli("localhost", "root", "", "mizuki_cakes");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$conn->query("
CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  price INT NOT NULL,
  img VARCHAR(255) NOT NULL,
  description TEXT NOT NULL
);
");

// Tạo bảng orders nếu chưa có (chỉ chạy 1 lần)
$conn->query("
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_date DATE NOT NULL,
  total INT NOT NULL
);
");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $img = $_POST['img'];
        $desc = $_POST['desc'];
        $sql = "INSERT INTO products (name, price, img, description) VALUES ('$name', '$price', '$img', '$desc')";
        $conn->query($sql);
    }
    if (isset($_POST['edit'])) {
        $id = $_POST['id'];
        $name = $_POST['name'];
        $price = $_POST['price'];
        $img = $_POST['img'];
        $desc = $_POST['desc'];
        $sql = "UPDATE products SET name='$name', price='$price', img='$img', description='$desc' WHERE id=$id";
        $conn->query($sql);
    }
    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $sql = "DELETE FROM products WHERE id=$id";
        $conn->query($sql);
    }
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$where = $search ? "WHERE name LIKE '%$search%'" : '';
$products = $conn->query("SELECT * FROM products $where ORDER BY id DESC");

$stat = $conn->query("SELECT COUNT(*) AS total, SUM(price) AS total_price FROM products")->fetch_assoc();

// Thống kê nâng cao
$max = $conn->query("SELECT * FROM products ORDER BY price DESC LIMIT 1")->fetch_assoc();
$min = $conn->query("SELECT * FROM products ORDER BY price ASC LIMIT 1")->fetch_assoc();
$avg = $conn->query("SELECT AVG(price) AS avg_price FROM products")->fetch_assoc();
$newest = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 1")->fetch_assoc();
$oldest = $conn->query("SELECT * FROM products ORDER BY id ASC LIMIT 1")->fetch_assoc();

// Xử lý lọc báo cáo
$from = isset($_GET['from']) ? $_GET['from'] : date('Y-m-01');
$to = isset($_GET['to']) ? $_GET['to'] : date('Y-m-d');
$report = [];
$labels = [];
$values = [];

if (isset($_GET['report'])) {
    $sql = "SELECT order_date, SUM(total) as total FROM orders WHERE order_date BETWEEN '$from' AND '$to' GROUP BY order_date ORDER BY order_date";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $report[] = $row;
        $labels[] = $row['order_date'];
        $values[] = $row['total'];
    }
}

// Thêm trường stock nếu chưa có
$conn->query("ALTER TABLE products ADD COLUMN IF NOT EXISTS stock INT NOT NULL DEFAULT 0;");

// Lấy dữ liệu tồn kho
$stock_report = [];
$stock_labels = [];
$stock_values = [];
$stock_sql = "SELECT name, stock FROM products ORDER BY id";
$stock_result = $conn->query($stock_sql);
while ($row = $stock_result->fetch_assoc()) {
    $stock_report[] = $row;
    $stock_labels[] = $row['name'];
    $stock_values[] = (int)$row['stock'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="utf-8" />
  <title>Admin – Mizuki’s Cakes</title>
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
    footer{background:var(--brown);color:#fff;margin-top:40px}
    .foot{display:grid;gap:18px;padding:26px 0}
    @media (min-width: 900px){
      .foot{grid-template-columns:1.2fr .8fr}
    }
    .copy{text-align:center;padding:14px 0;background:var(--brown-700);font-size:14px}
    .phone{position:fixed;right:18px;bottom:18px;z-index:60;background:var(--brown);color:#fff;width:52px;height:52px;border-radius:50%;display:grid;place-items:center;box-shadow:0 10px 24px rgba(0,0,0,.2)}
    /* Admin content */
    .admin-content{max-width:1100px;margin:30px auto;background:#fff;border-radius:14px;box-shadow:0 4px 24px rgba(0,0,0,.12);padding:32px;}
    h1{color:#b48d61;}
    .stat{margin-bottom:24px;padding:12px 18px;background:#fff7ee;border-radius:8px;display:flex;gap:32px;}
    .stat span{font-weight:600;color:#5b4632;}
    .search-box-admin{margin-bottom:18px;}
    .search-box-admin input{padding:8px 14px;border-radius:8px;border:1px solid #e0d3c2;width:220px;}
    .search-box-admin button{padding:8px 16px;border-radius:8px;background:#b48d61;color:#fff;border:none;font-weight:600;cursor:pointer;}
    table{width:100%;border-collapse:collapse;margin-bottom:24px;}
    th,td{border:1px solid #e0d3c2;padding:10px;text-align:left;}
    th{background:#f6efe6;}
    tr:nth-child(even){background:#fff7ee;}
    .actions button{margin-right:6px;}
    .form-section{background:#fff7ee;padding:18px;border-radius:10px;margin-bottom:24px;}
    .form-section input,.form-section textarea{width:100%;padding:8px;margin-bottom:10px;border-radius:6px;border:1px solid #e0d3c2;}
    .form-section button{background:#b48d61;color:#fff;padding:10px 18px;border:none;border-radius:8px;font-weight:600;cursor:pointer;}
    .form-section h2{margin-top:0;}
  </style>
</head>
<body>
  <!-- HEADER từ index.php -->
  <nav class="nav">
    <div class="container nav-inner">
      <div class="brand">
        <a href="index.php" style="color:inherit;text-decoration:none;">Mizuki’s Cakes</a>
      </div>
      <div class="menu">
        <a href="index.php#uu-dai">Ưu đãi</a>
        <a href="index.php#menu">Menu</a>
        <a href="index.php#ve-chung-toi">Về chúng tôi</a>
        <a class="cta" href="index.php#lien-he">Đặt bánh</a>
        <button class="cart-btn" aria-label="Giỏ hàng" onclick="window.location='cart.php'">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
            <circle cx="9" cy="21" r="1.5" fill="#b48d61"/>
            <circle cx="18" cy="21" r="1.5" fill="#b48d61"/>
            <path d="M3 5h2l1.68 10.39A2 2 0 0 0 8.65 17h7.7a2 2 0 0 0 1.97-1.61L21 7H6" stroke="#b48d61" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
          <span class="cart-badge">0</span>
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
  <!-- END HEADER -->

  <div class="admin-content">
    <h1>Quản trị sản phẩm Mizuki’s Cakes</h1>
    <div class="stat">
      <div>Tổng sản phẩm: <span><?= $stat['total'] ?></span></div>
      <div>Tổng giá trị: <span><?= number_format($stat['total_price'],0,',','.') ?>₫</span></div>
    </div>
    <form class="search-box-admin" method="get">
      <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Tìm kiếm sản phẩm...">
      <button type="submit">Tìm kiếm</button>
    </form>
    <div class="form-section">
      <h2>Thêm sản phẩm mới</h2>
      <form method="post">
        <input type="text" name="name" placeholder="Tên sản phẩm" required>
        <input type="number" name="price" placeholder="Giá (₫)" required>
        <input type="text" name="img" placeholder="Đường dẫn ảnh" required>
        <textarea name="desc" placeholder="Mô tả sản phẩm" required></textarea>
        <button type="submit" name="add">Thêm sản phẩm</button>
      </form>
    </div>
    <h2>Danh sách sản phẩm</h2>
    <table>
      <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Giá</th>
        <th>Mô tả</th>
        <th>Thao tác</th>
      </tr>
      <?php if ($products): while($row = $products->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><img src="<?= htmlspecialchars($row['img']) ?>" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;"></td>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= number_format($row['price'],0,',','.') ?>₫</td>
        <td><?= htmlspecialchars($row['description']) ?></td>
        <td class="actions">
          <!-- Sửa sản phẩm -->
          <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
            <input type="number" name="price" value="<?= $row['price'] ?>" required>
            <input type="text" name="img" value="<?= htmlspecialchars($row['img']) ?>" required>
            <input type="text" name="desc" value="<?= htmlspecialchars($row['description']) ?>" required>
            <button type="submit" name="edit">Sửa</button>
          </form>
          <!-- Xóa sản phẩm -->
          <form method="post" style="display:inline;">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button type="submit" name="delete" onclick="return confirm('Xóa sản phẩm này?')">Xóa</button>
          </form>
        </td>
      </tr>
      <?php endwhile; endif; ?>
    </table>
    <h2 id="baocao">Báo cáo & Thống kê</h2>
    <form method="get" style="margin-bottom:18px;">
      <label>Từ ngày: <input type="date" name="from" value="<?= htmlspecialchars($from) ?>"></label>
      <label>Đến ngày: <input type="date" name="to" value="<?= htmlspecialchars($to) ?>"></label>
      <button type="submit" name="report">Xem báo cáo thống kê</button>
    </form>

    <?php if (isset($_GET['report'])): ?>
      <h3>Bảng doanh thu theo ngày</h3>
      <table>
        <tr>
          <th>Ngày</th>
          <th>Doanh thu (₫)</th>
        </tr>
        <?php foreach ($report as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['order_date']) ?></td>
          <td><?= number_format($row['total'],0,',','.') ?>₫</td>
        </tr>
        <?php endforeach; ?>
      </table>
      <h3>Biểu đồ doanh thu</h3>
      <canvas id="chart" height="80"></canvas>
      <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
      <script>
        const ctx = document.getElementById('chart').getContext('2d');
        new Chart(ctx, {
          type: 'line',
          data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
              label: 'Doanh thu (₫)',
              data: <?= json_encode($values) ?>,
              borderColor: '#b48d61',
              backgroundColor: 'rgba(180,141,97,0.15)',
              fill: true,
              tension: 0.3
            }]
          },
          options: {
            responsive: true,
            plugins: {
              legend: { display: false }
            },
            scales: {
              y: { beginAtZero: true }
            }
          }
        });

        // Tự động cuộn tới báo cáo khi có kết quả
        window.onload = function() {
          if (window.location.search.includes('report')) {
            document.getElementById('baocao').scrollIntoView({behavior: 'smooth'});
          }
        }
      </script>
    <?php endif; ?>

    <h2 id="baocao">Báo cáo & Thống kê hàng tồn kho</h2>
    <table>
      <tr>
        <th>Tên sản phẩm</th>
        <th>Số lượng tồn kho</th>
      </tr>
      <?php foreach ($stock_report as $row): ?>
      <tr>
        <td><?= htmlspecialchars($row['name']) ?></td>
        <td><?= $row['stock'] ?></td>
      </tr>
      <?php endforeach; ?>
    </table>
    <h3>Biểu đồ tồn kho</h3>
    <canvas id="stockChart" height="80"></canvas>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
      const ctx = document.getElementById('stockChart').getContext('2d');
      new Chart(ctx, {
        type: 'bar',
        data: {
          labels: <?= json_encode($stock_labels) ?>,
          datasets: [{
            label: 'Tồn kho',
            data: <?= json_encode($stock_values) ?>,
            backgroundColor: '#b48d61'
          }]
        },
        options: {
          responsive: true,
          plugins: {
            legend: { display: false }
          },
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
      // Tự động cuộn tới báo cáo khi tải trang
      window.onload = function() {
        document.getElementById('baocao').scrollIntoView({behavior: 'smooth'});
      }
    </script>
  </div>

  <!-- FOOTER từ index.php -->
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
        <a class="btn" href="index.php#menu">Xem menu</a>
      </div>
    </div>
    <div class="copy">A PIECE OF CAKE, A PART OF WHOLE LIFE – Bánh Sẻ Chia, Trọn Cuộc Sống</div>
  </footer>
  <a class="phone" href="tel:2022104620221062" aria-label="Gọi đặt bánh">☎</a>
</body>
</html>