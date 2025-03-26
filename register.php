<?php
session_start();
ini_set('date.timezone', 'Asia/Shanghai');

// 数据库连接信息
$servername = "20.255.48.74";
$username_db = "www_wecf_life";
$password_db = "3Ap9ETimDmrr8pcC";
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
    $phone = $_POST['phone'];
    $mail = $_POST['mail'];
    $password = ($_POST['password']);

    // 防止重复注册
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->rowCount() > 0) {
        $error = "该用户名已注册";
    } else {
        try {
            // 插入待审核用户数据
            $stmt = $pdo->prepare("INSERT INTO users 
                (username, realname, phone, mail, password, gender, birth,  registration_date) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $username,
                $realname,
                $phone,
                $mail,
                $password,
                $_POST['gender'],
                $_POST['birth'],
                date('Y-m-d H:i:s')
            ]);

            // 在数据库中初始化游戏数据
            $stmt = $pdo->prepare("INSERT INTO game_status 
                (land_type_1_count,land_type_2_count,land_type_3_count,land_type_4_count,land_type_1_area,land_type_2_area,land_type_3_area,land_type_4_area,username) 
                VALUES (?,?,?,?,?,?,?,?,?)");
            $stmt->execute([0, 0, 0, 0, 0, 0, 0, 0, $username]);

            $success = "注册成功，可以登录";
        } catch (PDOException $e) {
            $error = "注册失败: " . $e->getMessage();
        }
    }
}

// CSS 样式嵌入
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>用户注册</title>
    <style>
        /* 通用样式 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
            font-size: 16px;
        }

        header {
            background-color: #ADD8E6;
            padding: 10px;
            text-align: center;
        }

        header img {
            width: 85px;
            height: 80px;
        }

        header h1 {
            font-size: 1.5rem;
            color: white;
            margin: 0;
            padding: 10px 0;
        }

        .form-container {
            padding: 15px;
            background-color: white;
            margin: 15px auto;
            width: 90%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-group label {
            margin-bottom: 8px;
            font-size: 1rem;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group button {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1rem;
            box-sizing: border-box;
        }

        .form-group .gender {
            display: flex;
            gap: 15px;
            margin-top: 8px;
        }

        .form-group .gender label {
            display: flex;
            align-items: center;
            margin: 0;
        }

        .form-group .gender input {
            width: auto;
            margin-right: 5px;
        }

        .form-group button {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            padding: 12px;
            font-size: 1rem;
            margin-top: 10px;
        }

        .form-group button:hover {
            background: #0056b3;
        }

        .login-link {
            text-align: center;
            margin-top: 15px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .success {
            color: green;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        /* 移动端特定样式 */
        @media (min-width: 768px) {
            .form-group {
                flex-direction: row;
                align-items: center;
            }

            .form-group label {
                flex: 0 0 100px;
                margin-right: 15px;
                margin-bottom: 0;
                text-align: right;
            }

            .form-group input,
            .form-group select,
            .form-group button {
                flex: 1;
            }

            .form-group .gender {
                margin-top: 0;
            }
        }

        /* 输入框优化 */
        input[type="text"],
        input[type="password"],
        input[type="email"],
        input[type="tel"],
        input[type="date"] {
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            height: 44px;
        }

        /* 日期选择器优化 */
        input[type="date"] {
            min-height: 44px;
        }

        /* 按钮点击效果 */
        button {
            -webkit-tap-highlight-color: transparent;
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