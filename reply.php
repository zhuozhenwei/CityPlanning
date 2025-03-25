<?php
// 检查是否已经登录
ini_set('date.timezone', 'Asia/Shanghai');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// 获取用户名
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取回复内容和帖子ID
    $postId = $_POST['id'];
    $replyContent = $_POST['reply'];

    // 敏感词过滤
    $filteredWords = ["iframe", "herf", "script", "习近平", "onerror", "?php", "javaScript", "tion.repla"];
    foreach ($filteredWords as $word) {
        if (stripos($replyContent, $word) !== false) {
            $error = "回复内容包含不允许的词语";
            break;
        }
    }

    if (!isset($error) && !empty($replyContent)) {
        // 连接数据库
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=www_wecf_life', 'www_wecf_life', '3Ap9ETimDmrr8pcC');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // 插入回复到数据库
            $stmt = $pdo->prepare("INSERT INTO replies (postid, author, content, timestamp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$postId, $username, $replyContent, date("Y-m-d H:i:s")]);

            // 重定向到查看帖子页面
            header("Location: view.php?id=$postId");
            exit();
        } catch (PDOException $e) {
            echo "数据库连接失败: " . $e->getMessage();
        }
    }
}
?>
