<?php
session_start();
$username = $_SESSION['username'];

if (empty($username)) {
    die(json_encode(["status" => "error", "message" => "用户未登录"]));
}

$land_type_1_count = $_POST['land_type_1_count'] ?? 0;
$land_type_2_count = $_POST['land_type_2_count'] ?? 0;
$land_type_3_count = $_POST['land_type_3_count'] ?? 0;
$land_type_4_count = $_POST['land_type_4_count'] ?? 0;

$host = '20.255.48.74';
$dbname = 'www_wecf_life';
$user = 'www_wecf_life';
$pass = '3Ap9ETimDmrr8pcC';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 获取用户ID
    $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die(json_encode(["status" => "error", "message" => "用户不存在"]));
    }

    $user_id = $user['id'];

    // 更新 game_status 表
    $stmt = $pdo->prepare("
        UPDATE game_status 
        SET land_type_1_count = ?,
            land_type_2_count = ?,
            land_type_3_count = ?,
            land_type_4_count = ?
        WHERE user_id = ?
    ");

    $stmt->execute([$land_type_1_count,$land_type_2_count,$land_type_3_count,$land_type_4_count, $user_id]);

    echo json_encode(["status" => "success", "message" => "更新成功"]);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "数据库更新失败: " . $e->getMessage()]));
}
?>