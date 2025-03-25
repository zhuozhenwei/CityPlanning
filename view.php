<?php
// 检查是否已登录
ini_set('date.timezone', 'Asia/Shanghai');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 获取用户名
$username = $_SESSION['username'];

// 连接数据库
try {
    $pdo = new PDO('mysql:host=localhost;dbname=www_wecf_life', 'root', '410926');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 删除帖子功能
    if (isset($_GET['delete_post']) && $_GET['delete_post'] == 1) {
        if (isset($_GET['id'])) {
            $postId = $_GET['id'];
            // 只能删除自己的帖子
            $stmt = $pdo->prepare("SELECT author FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($post && $post['author'] === $username) {
                // 删除帖子
                $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
                $stmt->execute([$postId]);
            }
            header("Location: index.php"); // 删除后跳转回首页
            exit();
        }
    }

    // 删除回复功能
    if (isset($_GET['delete_reply']) && $_GET['delete_reply'] == 1) {
        if (isset($_GET['reply_id']) && isset($_GET['post_id'])) {
            $replyId = $_GET['reply_id'];
            $postId = $_GET['post_id'];
            // 只能删除自己的回复
            $stmt = $pdo->prepare("SELECT author FROM replies WHERE id = ?");
            $stmt->execute([$replyId]);
            $reply = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($reply && $reply['author'] === $username) {
                // 删除回复
                $stmt = $pdo->prepare("DELETE FROM replies WHERE id = ?");
                $stmt->execute([$replyId]);
            }
            header("Location: view.php?id=" . $postId); // 删除回复后跳转到帖子页面
            exit();
        }
    }

    // 获取帖子ID
    if (isset($_GET['id'])) {
        $postId = $_GET['id'];

        // 获取帖子内容
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        // 如果帖子不存在
        if (!$post) {
            echo "<p>帖子不存在</p>";
            exit();
        }

        // 获取该帖子的所有评论
        $stmt = $pdo->prepare("SELECT * FROM replies WHERE postid = ? ORDER BY timestamp DESC");
        $stmt->execute([$postId]);
        $replies = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<p>无效的帖子ID</p>";
        exit();
    }

    // 处理回复提交
    if (isset($_POST['submit_reply'])) {
        $replyContent = $_POST['reply'];
        $replyImage = null;

        // 处理图片上传
        if ($_FILES['reply_image']['error'] == 0) {
            $imageTmpName = $_FILES['reply_image']['tmp_name'];
            $imageName = $_FILES['reply_image']['name'];
            $imagePath = 'uploads/replies/' . basename($imageName);
            
            // 验证图片格式
            $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = mime_content_type($imageTmpName);
            if (!in_array($fileType, $allowedMimeTypes)) {
                echo "<p>只能上传 JPEG, PNG 或 GIF 格式的图片。</p>";
                exit();
            }

            // 移动图片到目标目录
            if (move_uploaded_file($imageTmpName, $imagePath)) {
                $replyImage = $imagePath; // 保存图片路径
            } else {
                echo "<p>图片上传失败！</p>";
            }
        }

        // 插入回复到数据库
        $stmt = $pdo->prepare("INSERT INTO replies (postid, author, content, image, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$postId, $username, $replyContent, $replyImage]);

        // 跳转到帖子页面
        header("Location: view.php?id=" . $postId);
        exit();
    }

} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看帖子 | 微狮山</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
        }
        header {
            background-color: #ADD8E6;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            width: 100%;
            max-width: 50px;
            margin-bottom: 20px;
            position: relative;
        }
        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 20px;
            color: #fff;
            cursor: pointer;
        }
        main {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
        }
        .user-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
        .post-author {
            display: flex;
            align-items: center;
            position: relative;
        }
        .post-author span {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .author-info {
            margin-left: 10px;
        }
        .author-info p {
            margin: 0;
            font-size: 12px;
        }
        h1, h2 {
            color: #333;
        }
        .submit-btn {
            background-color: #4a235a;
            color: #fff;
            padding: 10px 20px;
            border: none;
            cursor: pointer;
        }
        .post-image, .reply-image {
            max-width: 100%;
            height: auto;
            margin-top: 10px;
        }
        /* 删除按钮 */
        .delete-btn {
            position: absolute;
            right: 10px;
            bottom: 10px;
            font-size: 20px;
            color: #999;
            cursor: pointer;
        }

        .delete-btn:hover {
            color: #e74c3c;
        }

        /* 上传按钮 */
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
        }
        .upload-btn input[type="file"] {
            display: none;
        }
        .upload-btn i {
            color: #888;
            font-size: 20px;
        }

        /* 灰色横线 */
        .reply-divider {
            border-top: 1px solid #ddd;
            margin: 20px 0;
        }
    </style>
</head>
<body>

<header>
    <span class="back-btn" onclick="window.location.href='index.php'">←</span>
</header>

<main>
    <section>
        <?php
        if ($post) {
            // 获取发帖人的头像
            $stmt = $pdo->prepare("SELECT avatar FROM users WHERE username = ?");
            $stmt->execute([$post['author']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $avatarPath = $user['avatar'] ? $user['avatar'] : 'uploads/avatars/default_avatar.png'; // 默认头像

            // 显示发帖人头像和帖子标题
            echo "<div class='post-author'>
                    <img src='" . htmlspecialchars($avatarPath) . "' alt='发帖人头像' class='user-avatar'>
                    <div class='author-info'>
                        <span>" . htmlspecialchars($post['author']) . "</span>
                        <p>发表于：" . date('Y-m-d H:i:s', strtotime($post['timestamp'])) . "</p>
                    </div>
                    " . ($post['author'] === $username ? "<a href='?delete_post=1&id=" . $post['id'] . "' class='delete-btn'>🗑️</a>" : "") . "
                </div>";
            echo "<h1>" . htmlspecialchars($post['title']) . "</h1>";
            echo "<p>" . nl2br(htmlspecialchars($post['content'])) . "</p>";

            // 显示帖子图片
            if ($post['image']) {
                echo "<img src='" . htmlspecialchars($post['image']) . "' alt='帖子图片' class='post-image'>";
            }
        }
        ?>

        <!-- 回复区域 -->
        <h2>回复</h2>
        <?php foreach ($replies as $reply) : ?>
            <div class="reply">
                <?php
                $stmt = $pdo->prepare("SELECT avatar FROM users WHERE username = ?");
                $stmt->execute([$reply['author']]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                $replyAvatarPath = $user['avatar'] ? $user['avatar'] : 'uploads/avatars/default_avatar.png'; // 默认头像
                ?>
                <div class="post-author">
                    <img src="<?php echo htmlspecialchars($replyAvatarPath); ?>" alt="回复人头像" class="user-avatar">
                    <div class="author-info">
                        <span><?php echo htmlspecialchars($reply['author']); ?></span>
                        <p>发表于：<?php echo date('Y-m-d H:i:s', strtotime($reply['timestamp'])); ?></p>
                    </div>
                    <?php echo ($reply['author'] === $username ? "<a href='?delete_reply=1&reply_id=" . $reply['id'] . "&post_id=" . $postId . "' class='delete-btn'>🗑️</a>" : ""); ?>
                </div>
                <p><?php echo nl2br(htmlspecialchars($reply['content'])); ?></p>
                <?php if ($reply['image']) : ?>
                    <img src="<?php echo htmlspecialchars($reply['image']); ?>" alt="回复图片" class="reply-image">
                <?php endif; ?>
                <div class="reply-divider"></div>
            </div>
        <?php endforeach; ?>

        <!-- 回复表单 -->
        <h3>发表评论</h3>
        <form action="view.php?id=<?php echo $postId; ?>" method="post" enctype="multipart/form-data">
            <textarea name="reply" rows="4" placeholder="输入你的回复..." required></textarea>
            <div class="upload-btn">
                <input type="file" name="reply_image">
                <i class="fas fa-camera"></i>
            </div>
            <button type="submit" name="submit_reply" class="submit-btn">提交回复</button>
        </form>
    </section>
</main>

<script>
    document.querySelector('.upload-btn').addEventListener('click', function() {
        document.querySelector('input[name="reply_image"]').click();
    });

    document.querySelector('input[name="reply_image"]').addEventListener('change', function() {
        const fileName = this.files[0] ? this.files[0].name : '没有选择文件';
        console.log(fileName);
    });
</script>

</body>
</html>
