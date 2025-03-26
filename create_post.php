<?php
// 假设你已经连接了数据库并初始化了会话
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username']; // 获取当前用户的用户名

// 处理表单提交
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 获取表单数据
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = null;

    // 处理图片上传
    if ($_FILES['image']['error'] == 0) {
        $imageTmpName = $_FILES['image']['tmp_name'];
        $imageName = $_FILES['image']['name'];
        $imagePath = 'uploads/posts/' . basename($imageName);

        // 验证图片格式
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($imageTmpName);
        if (!in_array($fileType, $allowedMimeTypes)) {
            echo "<p>只能上传 JPEG, PNG 或 GIF 格式的图片。</p>";
            exit();
        }

        // 移动图片到目标目录
        if (move_uploaded_file($imageTmpName, $imagePath)) {
            $image = $imagePath; // 保存图片路径
        } else {
            echo "<p>图片上传失败！</p>";
            exit();
        }
    }

    // 插入帖子数据到数据库
    try {
        $pdo = new PDO('mysql:host=20.255.48.74;dbname=www_wecf_life', 'www_wecf_life', '3Ap9ETimDmrr8pcC');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->prepare("INSERT INTO posts (title, content, author, image, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$title, $content, $username, $image]);

        // 跳转到帖子列表页面
        header("Location: index.php");
        exit();
    } catch (PDOException $e) {
        echo "数据库连接失败: " . $e->getMessage();
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>发帖 | 众策弈城</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #ADD8E6;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            min-height: 100vh;
            align-items: center;
        }
        .form-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 600px;
        }
        .form-container h1 {
            margin-bottom: 20px;
        }
        .form-container input, .form-container textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        .form-container textarea {
            height: 200px;
        }
        .form-container button {
            background-color: #ADD8E6;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .form-container button:hover {
            background-color: #6c3483;
        }
        /* 上传按钮样式 */
        .upload-btn {
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            margin-top: 10px;
        }
        .upload-btn input[type="file"] {
            display: none;
        }
        .upload-btn i {
            color: #888;
            font-size: 20px;
        }
        /* 图片预览样式 */
        #preview-img {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h1>发布新帖子</h1>
    <form action="create_post.php" method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="请输入帖子标题" required>
        <textarea name="content" placeholder="请输入帖子内容" required></textarea>

        <!-- 上传按钮 -->
        <label class="upload-btn">
            <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
            <i class="fas fa-camera"></i>
        </label>

        <!-- 图片预览 -->
        <img id="preview-img" src="" alt="预览图片" style="display: none;">

        <button type="submit">发布帖子</button>
    </form>
</div>

<script>
    // 图片预览功能
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function() {
            var preview = document.getElementById('preview-img');
            preview.src = reader.result;
            preview.style.display = 'block'; // 显示预览图片
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>
