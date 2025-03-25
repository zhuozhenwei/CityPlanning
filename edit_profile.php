<?php
ini_set('date.timezone', 'Asia/Shanghai');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// 数据库配置
$host = 'localhost';
$dbname = 'www_wecf_life';
$user = 'root';
$pass = '410926';

try {
    // 创建 PDO 数据库连接
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 获取用户当前信息
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = $_POST['username'];
    $newPhone = $_POST['phone'];
    $newEmail = $_POST['email'];

    $error = '';

    // 检查新用户名是否已存在
    if ($newUsername != $userData['username']) {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$newUsername]);
        if ($stmt->fetchColumn() > 0) {
            $error = '用户名已存在，请选择其他用户名';
        }
    }

    // 上传头像处理
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatarTmpName = $_FILES['avatar']['tmp_name'];
        $avatarName = $_FILES['avatar']['name'];
        $avatarType = $_FILES['avatar']['type'];
        $avatarSize = $_FILES['avatar']['size'];

        // 设置头像的保存路径
        $uploadDir = 'uploads/avatars/';
        $avatarPath = $uploadDir . basename($avatarName);

        // 检查文件类型（这里只接受图片文件）
        if (in_array($avatarType, ['image/jpeg', 'image/png', 'image/gif'])) {
            // 移动上传文件
            if (move_uploaded_file($avatarTmpName, $avatarPath)) {
                // 更新头像路径到数据库
                $stmt = $pdo->prepare("UPDATE users SET avatar = ? WHERE username = ?");
                if (!$stmt->execute([$avatarPath, $username])) {
                    $error = '头像上传失败，更新数据库时发生错误';
                    var_dump($stmt->errorInfo());
                }
            } else {
                $error = '头像上传失败，请重试';
            }
        } else {
            $error = '仅支持 JPEG, PNG 或 GIF 格式的头像';
        }
    }

    // 更新用户名、电话和邮箱
    if (empty($error)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, phone = ?, mail = ? WHERE username = ?");
        $result = $stmt->execute([$newUsername, $newPhone, $newEmail, $username]);

        if ($result) {
            // 更新成功后，重新设置 session
            $_SESSION['username'] = $newUsername;
            header("Location: edit_profile.php");
            exit();
        } else {
            $error = '数据库更新失败';
            var_dump($stmt->errorInfo()); // 输出错误信息，便于调试
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑个人信息</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        header {
            background-color: #ADD8E6;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            position: relative;
        }

        h1 {
            margin: 0;
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 24px;
            color: #fff;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn:hover {
            color: #ddd;
        }

        main {
            padding: 20px;
            background-color: #fff;
        }

        .error-message {
            color: red;
            text-align: center;
            margin-bottom: 20px;
        }

        .profile-section {
            margin-bottom: 15px;
        }

        .profile-section label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        .profile-section input[type="text"],
        .profile-section input[type="email"],
        .profile-section input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .profile-section button {
            background-color: #ADD8E6;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            width: 100%;
        }

        .profile-section button:hover {
            background-color: #8e24aa;
        }

        img {
            max-width: 100px;
            margin-top: 10px;
        }

        footer {
            background-color: #ADD8E6;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>
<body>
<header>
    <a href="index.php" class="back-btn">←</a>
    <h1>编辑个人信息</h1>
</header>

<main>
    <?php if (isset($error) && $error): ?>
        <div class="error-message"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
        <div class="profile-section">
            <label for="username">用户名</label>
            <input type="text" id="username" name="username"
                   value="<?php echo htmlspecialchars($userData['username']); ?>" required>
        </div>

        <div class="profile-section">
            <label for="phone">电话</label>
            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone']); ?>"
                   required>
        </div>

        <div class="profile-section">
            <label for="email">邮箱</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['mail']); ?>"
                   required>
        </div>

        <div class="profile-section">
            <label for="avatar">头像</label>
            <input type="file" id="avatar" name="avatar">
            <?php if ($userData['avatar']): ?>
                <img src="<?php echo htmlspecialchars($userData['avatar']); ?>" alt="Avatar">
            <?php endif; ?>
        </div>

        <div class="profile-section">
            <button type="submit">保存修改</button>
        </div>
    </form>
</main>

<footer>
    <p>© 2025 众策弈城，版权所有</p>
</footer>
</body>
</html>
