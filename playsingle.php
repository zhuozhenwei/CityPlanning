<!DOCTYPE html>
<?php
session_start();
$_SESSION['Round'] = 1;

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = '';
}
$username = $_SESSION['username'];
$host = '20.255.48.74';
$dbname = 'www_wecf_life';
$user = 'www_wecf_life';
$pass = '3Ap9ETimDmrr8pcC';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "连接失败: " . $e->getMessage();
    exit();
}

// 获取用户的个人信息
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// 处理AJAX请求更新游戏状态
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $land_type_1_count = $_POST['land_type_1_count'] ?? 0;
    $land_type_2_count = $_POST['land_type_2_count'] ?? 0;
    $land_type_3_count = $_POST['land_type_3_count'] ?? 0;
    $land_type_4_count = $_POST['land_type_4_count'] ?? 0;

    try {
        // 更新游戏状态
        $updateStmt = $pdo->prepare("UPDATE game_status SET 
                                    land_type_1_count = ?, 
                                    land_type_2_count = ?, 
                                    land_type_3_count = ?, 
                                    land_type_4_count = ? 
                                    WHERE username = ?");
        $updateStmt->execute([
            $land_type_1_count,
            $land_type_2_count,
            $land_type_3_count,
            $land_type_4_count,
            $username
        ]);

        echo json_encode(['status' => 'success', 'message' => '游戏状态更新成功']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => '更新失败: ' . $e->getMessage()]);
    }
    exit;
}

$module = mt_rand(100000, 999999);
$time = time();
$_SESSION['Token'] = md5($module . '#$@%!^*' . $time);
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script>
        var $id = "<?php session_start(); echo $_SESSION['username']; ?>";
        if ($id == "") {
            alert("您还没有登录");
            window.location.replace("./index.php");
        }
    </script>
    <title>Game Page</title>
    <style>
        #main {
            text-align: center;
            align-content: center;
            align-self: center;
            align-items: center;
            align-content: center;
            margin-left: 5%;
            width: 90%;
        }

        #t {
            border-width: 2px;
            border-style: double;
        }

        td {
            border-width: 1px;
            border-style: solid;
        }

        table {
            height: 722px;
            width: 430px;
            /* border-collapse: collapse; */
            /* background-image: url(./Bisai/PHPFiles/图片/t3.png);
            background-repeat: no-repeat;
            background-size: 100% 100%; */
        }

        #line1 {
            height: 153px;
            width: 370px;
        }

        .square {
            /* 正方形 */
            width: 100px;
            height: 146px;
            float: left;
            background-color: rgba(189, 220, 232, 0.616);
            border-width: 2px;
            color: black;
            border-style: solid;
        }

        #l1-1 {
            width: 48%;
            max-width: 946.5px;
            height: 174px;
            float: left;
            background-color: rgba(189, 232, 204, 0.616);
            color: black;
            border-style: solid;
        }

        #l1-2 {
            width: 48%;
            max-width: 946.5px;
            height: 174px;
            float: left;
            background-color: rgba(237, 208, 166, 0.616);
            color: black;
            border-style: solid;
        }

        .square,
        #l1-1, #l1-2 {
            box-sizing: border-box;
        }

        #judge1 {
            background-color: rgba(189, 220, 232, 0.616);
        }

        #l2-1 {
            width: 19%;
            height: 125px;
            float: left;
            background-color: rgba(251, 167, 165, 0.616);
            color: black;
            border-style: solid;
        }

        #l2-2 {
            width: 34%;
            height: 125px;
            float: left;
            background-color: rgba(249, 253, 166, 0.616);
            color: black;
            border-style: solid;
        }

        #l2-3 {
            width: 39%;
            height: 125px;
            float: left;
            background-color: rgba(189, 232, 204, 0.616);
            border-bottom-color: red;
            color: black;
            border-style: solid;
        }

        #l3-1 {
            height: 67px;
            width: 19%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            border-bottom-color: red;
            color: black;
            border-style: solid;
        }

        #l3-2 {
            height: 67px;
            width: 29%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            border-right-color: red;
            border-bottom-color: red;
            color: black;
            border-style: solid;
        }

        #l3-3 {
            height: 67px;
            width: 44%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            border-top-color: red;
            border-left-color: red;
            color: black;
            border-style: solid;
        }

        #l4-1 {
            height: 151px;
            width: 19%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            color: black;
            border-top-color: red;
            border-style: solid;
        }

        #l4-2 {
            height: 151px;
            width: 29%;
            float: left;
            background-color: rgba(251, 254, 200, 0.616);
            border-top-color: red;
            color: black;
            border-style: solid;
        }

        #l4-3 {
            height: 151px;
            width: 44%;
            float: left;
            background-color: rgba(251, 254, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #l5-1 {
            height: 121px;
            width: 19%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #l5-2 {
            height: 121px;
            width: 29%;
            float: left;
            background-color: rgba(189, 232, 204, 0.616);
            color: black;
            border-style: solid;
        }

        #l5-3 {
            height: 121px;
            width: 44%;
            float: left;
            background-color: rgba(251, 254, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #l6-1 {
            height: 70px;
            width: 24%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #l6-2 {
            height: 70px;
            width: 29%;
            float: left;
            background-color: rgba(251, 254, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #l6-3 {
            height: 70px;
            width: 39%;
            float: left;
            background-color: rgba(253, 201, 200, 0.616);
            color: black;
            border-style: solid;
        }

        #user2 {
            height: 85px;
            width: 250px;
            float: right;
            margin-top: 25px;
            margin-right: 50px;
            text-align: left;
            background-color: rgb(213, 224, 216);
            border-width: 1px;
            border-style: solid;
            border-radius: 15px;
        }

        #user2_i {
            height: 40px;
            width: 40px;
            float: right;
            margin-top: 30px;
            margin-left: 0px;
            margin-right: 30px;
            border-width: 3px;
            border-style: solid;
            border-radius: 45px;
        }

        #need {
            height: 40px;
            width: 40px;
            float: right;
            border-width: 3px;
            border-style: solid;
            border-radius: 45px;
            background-color: rgb(213, 224, 216);
        }

        h3 {
            float: right;
            margin: 0px;
            padding: 0px;
        }

        #cur1,
        #cur2,
        #cur3,
        #cur0 {
            background-color: rgb(213, 224, 216);
            border-radius: 30px;
            width: 20%;
            margin-left: 2%;
        }

        #data {
            float: right;
            margin-right: 40px;
            padding: 0px;
            border-width: 1px;
            border-style: solid;
            border-radius: 15px;
            background-color: rgb(213, 224, 216);
            font-size: 1.5em;
        }

        #jztb {
            width: 80%;
            height: 60%;
        }

        .status {
            visibility: hidden;
            width: 0px;
            height: 0px;
            float: left;
        }

        #nextRound {
            float: left;
            margin-top: 20px;
            margin-left: 40%;
            height: 60px;
            width: 85px;
            background-color: cornflowerblue;
            border-width: 3px;
            border-style: solid;
            border-radius: 15px;
        }

        * {
            transition: All 0.8s ease;
            -webkit-transition: All 0.8s ease;
            -o-transition: All 0.8s ease;
            -moz-transition: All 0.8s ease;
        }

        header {
            background-color: #ADD8E6;
            color: #fff;
            text-align: center;
            padding: 20px 0;
            position: relative;
            margin-bottom: 100px;
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            font-size: 24px;
            color: #000000;
            text-decoration: none;
            cursor: pointer;
        }

        .back-btn a {
            text-decoration: none;
        }
    </style>
</head>

<body onload="">
<header>
    <!--    <a href="index.php" class="back-btn">返回主页</a>-->
    <button class="back-btn"><a href="index.php">返回主页</a></button>
    <!--    <button class="back-btn">游戏详情</button>-->
    <h1>游戏</h1>
</header>

<div class="main" id="main">
    <div class="screen">
            <span id="l1-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                        class="status">-1</a>
                <h3>五湖潭公园</h3>
                <img src='./图片/ld.png' id='jztb'>
            </span>
        <span id="l1-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                    <img src='./图片/sy.png' id='jztb' style="margin-top: 30px"></span>
        <span id="l2-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l2-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l2-3" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                <h3>大明湖公园</h3>
                <img src='./图片/ld.png' id='jztb'>
            </span>
        <span id="l3-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                <h3>芙蓉街</h3>
            </span>
        <span id="l3-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                    <img src='./图片/gg1.png' id='jztb' style="height: 70px"></span>
        <span id="l3-3" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                    </span>
        <span id="l4-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                    <img src='./图片/gg1.png' id='jztb' style="margin-top:30px"></span>
        <span id="l4-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l4-3" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                <h3>曲水亭历史街区</h3>
                <img src='./图片/jz1.png' id='jztb' style="height:130px">
            </span>
        <span id="l5-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l5-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                <h3>珍珠泉公园</h3>
                <img src='./图片/ld.png' id='jztb'>
            </span>
        <span id="l5-3" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a>
                <h3>百花洲历史街区</h3>
                <img src='./图片/jz1.png' id='jztb'>
            </span>
        <span id="l6-1" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l6-2" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
        <span id="l6-3" onclick="changec(this)" onmouseover="show(this)" οnmοusedοwn="detail(event)"><a
                    class="status">-1</a></span>
    </div>
    <div id="user2">
        <button id="cur0" onclick="changecur(0)"><img src='./图片/素材/素材/棋子 公共服务与公共管理设施.png' id='need'
                                                      style='float:left;'><a>公共服务</a></button>
        <button id="cur1" onclick="changecur(1)"><img src='./图片/素材/素材/棋子 居住.png' id='need'
                                                      style='float:left;'><a>居住用地</a></button>
        <button id="cur2" onclick="changecur(2)"><img src='./图片/素材/素材/棋子 绿地.png' id='need'
                                                      style='float:left;'><a>公园绿地</a></button>
        <button id="cur3" onclick="changecur(3)"><img src='./图片/素材/素材/棋子 商业.png' id='need'
                                                      style='float:left;'> <a>商业设施</a></button>
    </div>
    <div style="text-align: center;">
        <?php
        echo $_SESSION['username'];
        ?>
    </div>
    <div id="data">
        当前资金：<a id="money">20</a>
        当前回合：<a id="round">1</a>
    </div>
</div>
<div>
    <button id="nextRound" onclick="nextr()">下一回合</button>
</div>
<?php
$module = mt_rand(100000, 999999);
$time = time();
//session_start();
$_SESSION['Token'] = md5($module . '#$@%!^*' . $time);
?>

<script>
    var round = 1;
    var cur = 0;
    var money = 20;
    var flags = new Array(); //用于记录每种区域类型的数量，初始为[0, 0, 0, 0]
    flags[0] = 0;
    flags[1] = 0;
    flags[2] = 0;
    flags[3] = 0;
    var arr = new Array(); //表示拆除每种区域类型所需的资金
    arr[0] = 6;
    arr[1] = 2;
    arr[2] = 1;
    arr[3] = 4;

    function check(round) {
        if (round == 10) {
            if (money >= 200) { // 成功
                var round1 = round;
                <?php session_start();$_SESSION['updating'] = 'true'; ?>;
                window.location.replace("./youwin.php?score=" + money + "&module=" + <?php echo $module; ?> +"&timestamp=" + <?php echo $time; ?> );
                // window.location.replace("./youwin.php");
            } else {
                window.location.replace("./youlose.php");
            }

            // window.location.replace("./playsingle.php");
        }
    }

    function show(params) {
        var text = params.firstChild.innerHTML;
        if (text == -1) {
            params.title = "当前场地尚未被开发";
        } else if (text == 0) {
            params.title = "当前场地属于公共服务与公共管理设施,拆除资金为6";
        } else if (text == 1) {
            params.title = "当前场地属于居住区域，拆除资金为2";
        } else if (text == 2) {
            params.title = "当前场地属于绿地，拆除资金为1";
        } else {
            params.title = "当前场地属于商业区域，拆除资金为4";
        }
        // params.title=params.innerHTML;
        // console.log(params.title);;
    }

    function changec(param) {
        // document.getElementById("1-1").style.backgroundColor="red";
        var c0 = "rgb(237,208,166)";
        var c1 = "rgb(249,253,166)";
        var c2 = "rgb(189,232,204)";
        var c3 = "rgb(251,167,165)";
        var color = param.backgroundColor;
        console.log(arr[param.firstChild.innerHTML]);
        if (cur == 0) {
            if (money < 10) { // 建一个公共服务地块需要10
                alert("资金不足！");
                return;
            }

            //这段代码的功能是在开发新地块之前，检查并扣除拆除原有地块的费用。如果资金不足，会弹出提示并终止操作；如果资金足够，则扣除费用并更新区域类型的数量。
            if (param.firstChild.innerHTML != -1) { // 检查当前地块是否已经被开发过，如果已被开发，就需要算上当前拆除所需的资金
                if (money < 10 + arr[param.firstChild.innerHTML]) { // 检查当前资金是否足够支付拆除原有地块的费用
                    alert("资金不足！");
                    return;
                }
                money -= arr[param.firstChild.innerHTML]; //拆除也花钱
                flags[param.firstChild.innerHTML]--; //更新 flags 数组，减少对应区域类型的数量
            }
            param.firstChild.innerHTML = 0;
            param.style.backgroundColor = c0;
            // console.log(param.firstChild);
            param.innerHTML = param.childNodes[0].outerHTML + "<img src='./图片/素材/素材/棋子 公共服务与公共管理设施.png' id='need' style='float:left;'> " + "<img src='./图片/sy.png' id='jztb'>";
            money -= 10;
            flags[0]++;
            // param.style.color="darkslategrey"
        } else if (cur == 1) {
            if (money < 5) {
                alert("资金不足！");
                return;
            }
            if (param.firstChild.innerHTML != -1) {
                if (money < 5 + arr[param.firstChild.innerHTML]) {
                    alert("资金不足！");
                    return;
                }
                money -= arr[param.firstChild.innerHTML];
                flags[param.firstChild.innerHTML]--;
            }
            param.firstChild.innerHTML = 1;
            param.style.backgroundColor = c1;
            param.innerHTML = param.childNodes[0].outerHTML + "<img src='./图片/素材/素材/棋子 居住.png' id='need' style='float:left;'> " + "<img src='./图片/jz1.png' id='jztb'>";
            flags[1]++;
            money -= 5;
            // param.style.color="black"
        } else if (cur == 2) {
            if (money < 3) {
                alert("资金不足！");
                return;
            }
            if (param.firstChild.innerHTML != -1) {
                if (money < 3 + arr[param.firstChild.innerHTML]) {
                    alert("资金不足！");
                    return;
                }
                money -= arr[param.firstChild.innerHTML];
                flags[param.firstChild.innerHTML]--;
            }
            param.firstChild.innerHTML = 2;
            param.style.backgroundColor = c2;
            param.innerHTML = param.childNodes[0].outerHTML + "<img src='./图片/素材/素材/棋子 绿地.png' id='need' style='float:left;'> " + "<img src='./图片/ld.png' id='jztb'>";
            money -= 3;
            flags[2]++;
        } else {
            if (money < 7) {
                alert("资金不足！");
                return;
            }
            if (param.firstChild.innerHTML != -1) {
                if (money < 7 + arr[param.firstChild.innerHTML]) {
                    alert("资金不足！");
                    return;
                }
                money -= arr[param.firstChild.innerHTML];
                flags[param.firstChild.innerHTML]--;
            }
            param.firstChild.innerHTML = 3;
            param.style.backgroundColor = c3;
            param.innerHTML = param.childNodes[0].outerHTML + "<img src='./图片/素材/素材/棋子 商业.png' id='need' style='float:left;'> " + "<img src='./图片/gg1.png' id='jztb'>";
            money -= 7;
            flags[3]++;
        }
        console.log(param.style.borderWidth);
        console.log(param.style.backgroundColor);
        document.getElementById("money").innerHTML = money;
        if (color == c0) {
            console.log("type1");
        }
    }

    function changecur(num) {
        cur = num;
    }

    function nextr() {
        money += flags[0] * 8 + flags[1] * 9 + flags[2] * 10 + flags[3] * 10;
        document.getElementById("money").innerHTML = money;
        document.getElementById('round').innerHTML = ++round;

        // 使用AJAX更新游戏状态
        var formData = new FormData();
        formData.append('land_type_1_count', flags[0]);
        formData.append('land_type_2_count', flags[1]);
        formData.append('land_type_3_count', flags[2]);
        formData.append('land_type_4_count', flags[3]);

        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    console.log('游戏状态更新成功:', data.message);
                } else {
                    console.error('游戏状态更新失败:', data.message);
                }
            })
            .catch(error => {
                console.error('请求失败:', error);
            });

        check(document.getElementById("round").innerHTML);
    }

</script>
</body>

</html>