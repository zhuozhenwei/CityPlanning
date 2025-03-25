<?php
session_start();
ini_set('date.timezone', 'Asia/Shanghai');

// 数据库连接信息
$servername = "localhost";
$username_db = "root";
$password_db = "410926";
$dbname = "www_wecf_life";

try {
    // 创建数据库连接
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username_db, $password_db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "连接失败: " . $e->getMessage();
    exit();
}

// 注册逻辑
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $realname = $_POST['realname'];
    $grade = $_POST['grade'];
    $class = $_POST['class'];
    $phone = $_POST['phone'];
    $mail = $_POST['mail'];
    $password = md5($_POST['password']); // 使用 MD5 加密密码


    // 防止重复注册
    $stmt = $pdo->prepare("SELECT * FROM pending_users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $error = "该用户名已注册，请等待管理员审核。";
    } else {
        // 插入待审核用户数据
        $stmt = $pdo->prepare("INSERT INTO pending_users (username, realname, grade, class, phone, mail, password,  status, registration_date) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$username, $realname, $grade, $class, $phone, $mail, $password, 'pending', date('Y-m-d H:i:s')]);

        $success = "注册成功，等待管理员审核通过后可以登录。";
    }
}

// CSS 样式嵌入
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>用户注册</title>
    <style>
        /* 通用样式 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        header {
            background-color: #ADD8E6; /* 紫色背景 */
            padding: 10px;
            text-align: center;
        }

        header img {
            width: 85px;
            height: 80px;
        }

        header h1 {
            font-size: 24px;
            color: white; /* 白色文字 */
        }

        .form-container {
            padding: 20px;
            background-color: white;
            margin: 30px auto;
            width: 40%;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .form-group label {
            flex: 0 0 100px; /* 左侧标签固定宽度 */
            margin-right: 15px; /* 左侧标签与右侧输入框的间隔 */
            text-align: right;
            font-size: 16px;
            color: #333;
        }

        .form-group input, .form-group select, .form-group button {
            flex: 1; /* 右侧输入框和按钮占据剩余空间 */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-group .gender {
            display: flex;
            gap: 10px;
        }

        .form-group .gender label {
            flex: none;
            margin-right: 5px;
            gap: 5px;
        }

        .form-group button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .form-group button:hover {
            background: #0056b3;
        }

        .login-link {
            text-align: center;
            margin-top: 10px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>众策弈城 新用户注册</h1>
</header>

<div class="form-container">
    <form action="register.php" method="POST">
        <?php if (isset($error)): ?>
            <p class="error"><?= $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p class="success"><?= $success; ?></p>
        <?php endif; ?>

        <div class="form-group">
            <label for="username">用户名</label>
            <input type="text" name="username" id="username" placeholder="请输入账号" required>
        </div>

        <div class="form-group">
            <label for="password">密码</label>
            <input type="password" name="password" id="password" placeholder="请输入密码" required>
        </div>

        <div class="form-group">
            <label for="realname">姓名</label>
            <input type="text" name="realname" id="realname" placeholder="请输入真实姓名" required>
        </div>

        <div class="form-group">
            <label for="mail">邮箱</label>
            <input type="email" name="mail" id="mail" placeholder="请输入邮箱" required>
        </div>

        <div class="form-group">
            <label for="phone">手机号</label>
            <input type="tel" name="phone" id="phone" placeholder="请输入您的手机号" required>
        </div>

        <div class="form-group">
            <label>性别</label>
            <div class="gender">
                <label><input type="radio" name="gender" value="male"> 男</label>
                <label><input type="radio" name="gender" value="female"> 女</label>
            </div>
        </div>

        <div class="form-group">
            <label for="birth">出生日期</label>
            <input type="date" name="birth" id="birth" value="2000-01-01" required>
        </div>

        <div class="form-group">
            <button type="submit">注册</button>
        </div>

        <div class="login-link">
            <a href="./login.php" target="_blank">已有账号？立即登录</a>
        </div>
    </form>
</div>
</body>
</html>

