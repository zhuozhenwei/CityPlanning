<?php
// 连接数据库
$pdo = new PDO("mysql:host=localhost;dbname=www_wecf_life", "www_wecf_life", "3Ap9ETimDmrr8pcC");

// 检查管理员是否登录
session_start();
if ($_SESSION['username']!="SS7D"||$_SESSION['username']!="admin") {
    header("Location: login.php");
    exit();
}
// 查询待审核的用户
$query = "SELECT * FROM pending_users WHERE status = 'pending'";
$stmt = $pdo->query($query);
$pending_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 处理审核操作
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];
    
    // 审核通过或拒绝
    if ($action === 'approve') {
        // 获取用户信息
        $select_query = "SELECT * FROM pending_users WHERE id = :id";
        $select_stmt = $pdo->prepare($select_query);
        $select_stmt->bindParam(':id', $user_id);
        $select_stmt->execute();
        $user = $select_stmt->fetch(PDO::FETCH_ASSOC);

        // 将用户信息插入到 users 表
        $insert_query = "INSERT INTO users (username, realname, grade, class, phone, mail, password, avatar, registration_date)
                         VALUES (:username, :realname, :grade, :class, :phone, :mail, :password, :avatar, :registration_date)";
        $insert_stmt = $pdo->prepare($insert_query);
        $insert_stmt->bindParam(':username', $user['username']);
        $insert_stmt->bindParam(':realname', $user['realname']);
        $insert_stmt->bindParam(':grade', $user['grade']);
        $insert_stmt->bindParam(':class', $user['class']);
        $insert_stmt->bindParam(':phone', $user['phone']);
        $insert_stmt->bindParam(':mail', $user['mail']);
        $insert_stmt->bindParam(':password', $user['password']);
        $insert_stmt->bindParam(':avatar', $user['avatar']);
        $insert_stmt->bindParam(':registration_date', $user['registration_date']);
        $insert_stmt->execute();

        // 删除待审核用户
        $delete_query = "DELETE FROM pending_users WHERE id = :id";
        $delete_stmt = $pdo->prepare($delete_query);
        $delete_stmt->bindParam(':id', $user_id);
        $delete_stmt->execute();
        
    } elseif ($action === 'reject') {
        // 拒绝用户，直接更新状态
        $update_query = "UPDATE pending_users SET status = 'rejected' WHERE id = :id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':id', $user_id);
        $update_stmt->execute();
    }

    // 重定向回审核页面
    header("Location: sh.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员审核用户</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        header {
            background-color: #ADD8E6;
            color: white;
            padding: 15px;
            text-align: center;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #ADD8E6;
            color: white;
        }

        .action-btn {
            padding: 5px 10px;
            margin: 0 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .approve-btn {
            background-color: #2ecc71;
            color: white;
        }

        .reject-btn {
            background-color: #e74c3c;
            color: white;
        }

        .approve-btn:hover, .reject-btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <header>
        <h1>管理员审核用户</h1>
    </header>

    <div class="container">
        <h2>待审核的用户</h2>
        <table>
            <thead>
                <tr>
                    <th>用户名</th>
                    <th>真实姓名</th>
                    <th>性别</th>
                    <th>年龄</th>
                    <th>手机号码</th>
                    <th>邮箱</th>
                    <th>注册日期</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_users as $user): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                        <td><?php echo htmlspecialchars($user['realname']); ?></td>
                        <td><?php echo htmlspecialchars($user['grade']); ?></td>
                        <td><?php echo htmlspecialchars($user['class']); ?></td>
                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                        <td><?php echo htmlspecialchars($user['mail']); ?></td>
                        <td><?php echo htmlspecialchars($user['registration_date']); ?></td>
                        <td>
                            <form method="POST" action="sh.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="action" value="approve" class="action-btn approve-btn">通过</button>
                            </form>
                            <form method="POST" action="sh.php" style="display:inline;">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" name="action" value="reject" class="action-btn reject-btn">拒绝</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
