<?php
// auth.php - Đăng nhập / Đăng ký Mizuki’s Cakes
session_start();

$conn = new mysqli("localhost", "root", "", "mizuki_cakes");
if ($conn->connect_error) die("Kết nối thất bại: " . $conn->connect_error);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    // Kiểm tra tài khoản
    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password' LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $user;
        // Nếu là admin thì chuyển sang admin.php
        if ($user['role'] === 'admin') {
            header("Location: admin.php");
            exit();
        } else {
            header("Location: index.php"); // Khách hàng chuyển về trang chủ
            exit();
        }
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $fullname = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repassword = $_POST['repassword'];

    // Kiểm tra mật khẩu nhập lại
    if ($password !== $repassword) {
        $error = "Mật khẩu nhập lại không khớp!";
    } else {
        // Kiểm tra trùng username hoặc email
        $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");
        if ($check && $check->num_rows > 0) {
            $error = "Tên đăng nhập hoặc email đã tồn tại!";
        } else {
            // Thêm tài khoản mới vào database
            $sql = "INSERT INTO users (fullname, username, email, password, role) VALUES ('$fullname', '$username', '$email', '$password', 'user')";
            if ($conn->query($sql)) {
                // Đăng ký thành công, tự động đăng nhập và chuyển về trang chủ
                $_SESSION['user'] = [
                    'fullname' => $fullname,
                    'username' => $username,
                    'email' => $email,
                    'role' => 'user'
                ];
                header("Location: index.php");
                exit();
            } else {
                $error = "Đăng ký thất bại. Vui lòng thử lại!";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Đăng nhập / Đăng ký - Mizuki’s Cakes</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      background: #f6efe6;
      font-family: 'Poppins', Arial, sans-serif;
      margin: 0;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .auth-container {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 8px 32px rgba(0,0,0,.12);
      padding: 36px 32px 28px 32px;
      width: 100%;
      max-width: 407px;
      text-align: center;
    }
    .auth-tabs {
      display: flex;
      justify-content: center;
      margin-bottom: 28px;
      gap: 12px;
    }
    .auth-tab {
      flex: 1;
      padding: 10px 0;
      border: none;
      background: #f6efe6;
      color: #5b4632;
      font-weight: 600;
      font-size: 16px;
      border-radius: 8px 8px 0 0;
      cursor: pointer;
      transition: background .18s;
    }
    .auth-tab.active {
      background: #b48d61;
      color: #fff;
    }
    .auth-form {
      display: none;
      flex-direction: column;
      gap: 16px;
      margin-top: 8px;
    }
    .auth-form.active {
      display: flex;
    }
    .auth-form input {
      padding: 10px 12px;
      border-radius: 8px;
      border: 1px solid #e0d3c2;
      font-size: 15px;
      font-family: inherit;
      background: #f9f6f2;
      color: #5b4632;
    }
    .auth-form input:focus {
      border-color: #b48d61;
      outline: none;
    }
    .auth-form button {
      background: #b48d61;
      color: #fff;
      border: none;
      border-radius: 8px;
      padding: 10px 0;
      font-size: 16px;
      font-weight: 600;
      cursor: pointer;
      transition: background .18s;
    }
    .auth-form button:hover {
      background: #5b4632;
    }
    .auth-link {
      margin-top: 16px;
      font-size: 14px;
      color: #5b4632;
      opacity: .8;
    }
    .auth-link a {
      color: #b48d61;
      text-decoration: underline;
      cursor: pointer;
    }
    /* Thêm logo và link về trang chủ */
    .auth-logo {
      margin-bottom: 18px;
      display: flex;
      justify-content: center;
    }
    .auth-logo img {
      width: 54px;
      height: 54px;
      border-radius: 50%;
      object-fit: cover;
      box-shadow: 0 2px 10px rgba(0,0,0,.08);
      margin-right: 10px;
    }
    .auth-logo span {
      font-weight: 700;
      font-size: 20px;
      color: #b48d61;
      align-self: center;
      letter-spacing: .12em;
    }
    .error-msg {
      color: #e74c3c;
      font-size: 15px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="auth-container">
    <a href="index.php" class="auth-logo" title="Về trang chủ">
      <img src="images/slideshow_1.png" alt="Mizuki’s Cakes">
      <span>Mizuki’s Cakes</span>
    </a>
    <div class="auth-tabs">
      <button class="auth-tab active" id="loginTab" onclick="showForm('login')">Đăng nhập</button>
      <button class="auth-tab" id="registerTab" onclick="showForm('register')">Đăng ký</button>
    </div>
    <form class="auth-form active" id="loginForm" autocomplete="off" method="post" action="">
      <?php if ($error): ?>
        <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>
      <input type="text" name="username" placeholder="Tên đăng nhập" required>
      <input type="password" name="password" placeholder="Mật khẩu" required>
      <button type="submit" name="login">Đăng nhập</button>
      <div class="auth-link">Chưa có tài khoản? <a onclick="showForm('register')">Đăng ký</a></div>
    </form>
    <form class="auth-form" id="registerForm" autocomplete="off" method="post" action="">
      <input type="text" name="fullname" placeholder="Họ và tên" required>
      <input type="text" name="username" placeholder="Tên đăng nhập" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Mật khẩu" required>
      <input type="password" name="repassword" placeholder="Nhập lại mật khẩu" required>
      <button type="submit" name="register">Đăng ký</button>
      <div class="auth-link">Đã có tài khoản? <a onclick="showForm('login')">Đăng nhập</a></div>
    </form>
  </div>
  <script>
    function showForm(type) {
      document.getElementById('loginForm').classList.toggle('active', type === 'login');
      document.getElementById('registerForm').classList.toggle('active', type === 'register');
      document.getElementById('loginTab').classList.toggle('active', type === 'login');
      document.getElementById('registerTab').classList.toggle('active', type === 'register');
    }
  </script>
</body>
</html>