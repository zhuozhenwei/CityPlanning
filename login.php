<?php
session_start();

if (isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// 数据库配置
$host = 'localhost'; 
$dbname = 'www_wecf_life';  
$user = 'www_wecf_life';
$pass = '3Ap9ETimDmrr8pcC';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败");
}

if (isset($_POST['submit'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "请填写全部字段";
    } else {
        // 对密码进行 MD5 哈希
        $hashedPassword = md5($password);

        // 查询数据库以验证用户名和密码
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
        $stmt->execute([
            'username' => $username,
            'password' => $hashedPassword
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $_SESSION['username'] = $username;
            header("Location: index.php");
            exit();
        } else {
            $error = "用户名或密码错误";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登录 | 众策弈城</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .login-form {
            display: flex;
            flex-direction: column;
        }

        .login-form input {
            margin: 10px 0;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .login-form button {
            padding: 10px;
            font-size: 16px;
            background-color: #ADD8E6;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-form button:hover {
            background-color: #ADD8E6;
        }

        .error, .success {
            text-align: center;
            color: red;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .success {
            color: green;
        }

        .register-link {
            text-align: center;
            font-size: 14px;
        }

        .register-link a {
            color: #3498db;
            text-decoration: none;
        }

        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>登录众策弈城</h2>
        <?php
        // 显示错误信息或成功信息
        if (isset($error)) {
            echo "<p class='error'>$error</p>";
        }
        if (isset($_GET['success'])) {
            echo "<p class='success'>" . $_GET['success'] . "</p>";
        }
        ?>
        <form action="login.php" method="post" class="login-form">
            <input type="text" name="username" placeholder="用户名" required>
            <input type="password" name="password" placeholder="密码" required>
            <button type="submit" name="submit">登录</button>
        </form>
        <p class="register-link"><a href="register.php">注册账号</a></p>
    </div>
</body>
</html>
