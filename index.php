<!-- 引入 FontAwesome 图标库 -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<?php
//设置默认时区为上海时间
ini_set('date.timezone', 'Asia/Shanghai');
session_start(); //在服务器端存储用户数据的方式
if (!isset($_SESSION['username'])) { // 检查会话中是否存在username
    header("Location: login.php"); // 用于重定向
    exit();
}

$username = $_SESSION['username'];

// 数据库配置
$host = '20.255.48.74';
$dbname = 'www_wecf_life';
$user = 'www_wecf_life';
$pass = '3Ap9ETimDmrr8pcC';

try {
    // 创建 PDO 数据库连接
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); //抛出PDOException异常
} catch (PDOException $e) {
    die("数据库连接失败: " . $e->getMessage());
}

// 获取最近发布的帖子（sticky=1 的帖子置顶）
$recentPosts = [];
$stmt = $pdo->prepare("SELECT * FROM posts ORDER BY sticky DESC, timestamp DESC LIMIT 10");
$stmt->execute();
$recentPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 获取用户的个人信息
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
$stmt->execute([$username]);
$userInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// 获取用户的游戏状态和收益信息
$stmt = $pdo->prepare("SELECT land_type_1_count, land_type_2_count, land_type_3_count, land_type_4_count,land_type_1_area, land_type_2_area, land_type_3_area, land_type_4_area FROM game_status WHERE username = ?");
$stmt->execute([$username]);
$gameStatus = $stmt->fetch(PDO::FETCH_ASSOC);

// 如果 $gameStatus 是 false 或未定义，则初始化为默认数组
$gameStatus = is_array($gameStatus) ? $gameStatus : [
    'land_type_1_count' => 0,
    'land_type_2_count' => 0,
    'land_type_3_count' => 0,
    'land_type_4_count' => 0
];

//if (!$gameStatus) {
//    $stmt = $pdo->prepare("INSERT INTO game_status (user_id, has_played, earnings, land_type_1_count, land_type_2_count, land_type_3_count, land_type_4_count) VALUES (?, FALSE, 0.00,0,0,0,0)");
//    $stmt->execute([$userInfo['id']]);
//    $gameStatus = ['has_played' => FALSE, 'earnings' => 0.00, 'land_type_1_count' => 0, 'land_type_2_count' => 0, 'land_type_3_count' => 0, 'land_type_4_count' => 0];
//}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>首页 | 众策弈城</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/echarts@5.6.0/dist/echarts.min.js"></script>
    <script>
        function checkNewReplies() { //使用 jQuery 的 AJAX 方法发送请求
            $.ajax({
                url: 'check_new_replies.php',
                method: 'GET',
                success: function(response) {
                    if (response.newReplies > 0) {
                        alert('您收到了 ' + response.newReplies + ' 条新回复！');
                    }
                },
                complete: function() {
                    setTimeout(checkNewReplies, 5000);
                }
            });
        }

        $(document).ready(function() { //页面加载时启动轮询
            checkNewReplies();
        });

        // 点击"我的"按钮时隐藏发帖的加号按钮
        function showRecentPosts() {
            document.getElementById("recentPosts").style.display = "block";
            document.getElementById("gameInfo").style.display = "none";
            document.getElementById("userInfo").style.display = "none";
            document.getElementById("homeBtn").classList.add("active");
            document.getElementById("displayBtn").classList.remove("active");
            document.getElementById("profileBtn").classList.remove("active");
            document.querySelector('.floating-btn').style.display = "block"; // 显示加号按钮
        }

        function showGameInfo() {
            document.getElementById("gameInfo").style.display = "block";
            document.getElementById("recentPosts").style.display = "none";
            document.getElementById("userInfo").style.display = "none";
            document.getElementById("displayBtn").classList.add("active");
            document.getElementById("profileBtn").classList.remove("active");
            document.getElementById("homeBtn").classList.remove("active");
            document.querySelector('.floating-btn').style.display = "none"; // 隐藏加号按钮
        }

        function showUserInfo() {
            document.getElementById("recentPosts").style.display = "none"; //隐藏recentPosts
            document.getElementById("gameInfo").style.display = "none";
            document.getElementById("userInfo").style.display = "block"; // 显示userInfo
            document.getElementById("profileBtn").classList.add("active");
            document.getElementById("displayBtn").classList.remove("active");
            document.getElementById("homeBtn").classList.remove("active");
            document.querySelector('.floating-btn').style.display = "none"; // 隐藏加号按钮
        }
    </script>
    <link rel="stylesheet" href="style3.css">
    <style>
        /* 通用样式 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f7f7;
        }

        header {
            background-color: #ADD8E6;
            /* 紫色背景 */
            padding: 10px;
            text-align: center;
        }

        header img {
            width: 85px;
            height: 80px;
        }

        header h1 {
            font-size: 24px;
            color: white;
            /* 白色文字 */
        }

        .user-info {
            font-size: 14px;
            margin-top: 10px;
        }

        .user-info a {
            text-decoration: none;
            color: #3498db;
        }

        .post-list {
            padding: 20px;
        }

        .post-item {
            padding: 15px;
            margin-bottom: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .post-item a {
            text-decoration: none;
            color: #333;
        }

        .post-item h2 {
            font-size: 18px;
        }

        .post-item p {
            font-size: 14px;
            color: #555;
        }

        /* 底部导航栏样式 */
        .footer-nav {
            position: fixed;
            bottom: 0;
            width: 100%;
            background-color: #ADD8E6;
            /* 紫色背景 */
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }

        .footer-nav a {
            text-decoration: none;
            color: white;
            font-size: 14px;
            text-align: center;
        }

        .footer-nav a i {
            display: block;
            font-size: 24px;
        }

        .footer-nav a.active {
            color: #f39c12;
            /* 选中的按钮变为金色 */
        }

        /* 个人信息样式 */
        #userInfo {
            padding: 20px;
            display: none;
        }

        #gameInfo {
            padding: 20px;
            display: none;
        }

        #recentPosts {
            display: block;
        }

        #main {
            width: 600px;
            height: 400px;
            margin: 0 auto;
        }

        /* 个人信息头像 */
        .profile-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 20px;
        }

        .user-info-detail {
            font-size: 16px;
            margin-bottom: 10px;
        }

        /* 悬浮的发布新帖按钮 */
        .floating-btn {
            position: fixed;
            right: 20px;
            bottom: 80px;
            /* 提高按钮的位置 */
            width: 60px;
            height: 60px;
            background-color: #ADD8E6;
            color: white;
            border-radius: 50%;
            font-size: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .floating-btn:hover {
            background-color: #9b59b6;
        }

        /* 加号的样式，确保加号是白色的 */
        .floating-btn i {
            color: white;
        }

        /* 个人信息中的编辑资料按钮 */
        .edit-profile-btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #ADD8E6;
            /* 紫色背景，与顶部一致 */
            color: white;
            text-align: center;
            border-radius: 5px;
            margin-top: 20px;
            text-decoration: none;
        }

        .edit-profile-btn:hover {
            background-color: #2980b9;
        }

        .games {
            width: 150px;
            height: 50px;
            /* background-position-y: 50px; */
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
            /*margin-left: 15%;*/
            /*margin-right: 15%;*/
            /*font-family: '千绘图文','幼圆';*/
            font-size: 20px;
            font-weight: bolder;
            cursor: pointer;
        }

        .titlestyle {
            color: #0074D9;
            /*设置文字颜色*/
            font-size: 15px;
            /*设置字体大小*/
            font-weight: bolder;
            /*设置字体粗细*/
            -webkit-animation: flicker 2s infinite;
            /*设置动画*/
            text-align: center;
            /*margin-bottom: 20px;*/
        }

        @-webkit-keyframes flicker {

            /*创建动画*/
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>

    <header>
        <div class="header">
            <h1>众策弈城</h1>
            <div class="user-info">
                <!--PHP 函数，用于将特殊字符转换为 HTML 实体，防止 XSS-->
                <span>欢迎, <?php echo htmlspecialchars($username); ?></span> |
                <a href="./logout.php">退出</a>
            </div>
        </div>
    </header>

    <main>
        <!-- 显示最近发布的帖子 -->
        <div id="recentPosts">
            <div class="post-list">
                <?php foreach ($recentPosts as $post): ?>
                    <div class="post-item">
                        <h2>
                            <a href="view.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a>
                        </h2>
                        <p class="author-info">By <?php echo htmlspecialchars($post['author']); ?>
                            | <?php echo htmlspecialchars($post['timestamp']); ?></p>
                        <p class="post-content">
                            <?php
                            $maxChars = 150; //内容的最大显示字符数
                            $content = $post['content']; //内容
                            if (mb_strlen($content, 'utf-8') > $maxChars) {
                                $shortContent = mb_substr($content, 0, $maxChars, 'utf-8'); //将截取后的内容赋值给 $shortContent
                                echo "{$shortContent} <a href='view.php?id={$post['id']}' class='show-full-text'>显示全文</a>"; //只显示截断的内容
                            } else {
                                echo $content;
                            }
                            ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- 显示个人信息 -->
        <div id="userInfo">
            <h2>个人信息</h2>
            <div class="profile-avatar">
                <img src="<?php echo htmlspecialchars($userInfo['avatar']); ?>" alt="头像" class="profile-avatar">
            </div>
            <p class="user-info-detail"><strong>用户名:</strong> <?php echo htmlspecialchars($userInfo['username']); ?></p>
            <p class="user-info-detail"><strong>真实姓名:</strong> <?php echo htmlspecialchars($userInfo['realname']); ?>
            </p>
            <p class="user-info-detail"><strong>电话:</strong> <?php echo htmlspecialchars($userInfo['phone']); ?></p>
            <p class="user-info-detail"><strong>邮箱:</strong> <?php echo htmlspecialchars($userInfo['mail']); ?></p>

            <!-- 编辑资料按钮 -->
            <a href="edit_profile.php" class="edit-profile-btn">编辑资料</a>
        </div>

        <!--  游戏信息  -->
        <div id="gameInfo">
            <!--   刚注册，还没有开始玩游戏   -->
            <?php if ($gameStatus['land_type_1_count'] == 0 && $gameStatus['land_type_2_count'] == 0 && $gameStatus['land_type_3_count'] == 0 && $gameStatus['land_type_4_count'] == 0): ?>
                <div class="titlestyle">
                    <h2>游戏规则</h2>
                </div>
                <h3 class="titlestyle" style="font-size: 15px; text-align: left;">模式选择：</h3>
                <p style="text-indent: 2em; color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    交互模式可分为协同模式和博弈模式，协同模式可由2-6匹配，一人及以上即可开始参与，形成一个共同完成同一地块更新操作的小组；博弈模式可由2-6人匹配开始参与，两人及以上即可开始参与，形成两个1-3人相互对抗的小组，每个小组完成1/2地块的更新操作。选取某一固定待开发区域。根据用地单元边界随机切分地块为误差允许范围内综合效益均等性及用地面积均等性切分为基本等同的两份，随机切分性增加了参与平台的趣味性，使用空间句法技术将开发区随机生成两个综合价值基本等同的区域，便于协同或博弈参与模式的多人操作,从而收集更多主体的诉求。</p>
                <h3 class="titlestyle" style="font-size: 15px; text-align: left;">收益计算：</h3>
                <p style="text-indent: 2em; color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    每个参与者具有起始资本，收益通过计算周边地块变动和新建收益计算。取控制性详细规划的20年为一个回合，一共可以进行十次操作，最终以得分高的作为胜出者。为了提高吸引力，参与系统无时间限制，因地块用地性质不同，存在真实城市规划管理中的用地管控规则。
                    对应不同的网格单元的单击可以弹出选项：</p>
                <ul style="color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    <li>拆除：成本减少</li>
                    <li>新建：成本减少，更新用地建成后获得利润等选项</li>
                    <li>意见输入：公众可以自由输入对该城市要素的更新意见</li>
                </ul>
                <p style="text-indent: 2em; color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    其中拆除及更新表现为棋子的替换操作，保留表现为棋子的保留操作。每一枚棋子的价值根据地块总价值和网格单元的数量取均值。</p>
                <h3 class="titlestyle" style="font-size: 15px; text-align: left;">价值计算原则</h3>
                <p style="text-indent: 2em; color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    价值计算原则采用累计积分制。每两年为一周期进行累计积分计算回合累计收益,以二十年为一个轮回计算总收益，总收益计算标准以城市经济社会学为依据，将生态效益和经济效益各取权重值计算总效益。以最终的累计效益作为评判胜负的依据。
                    系统设定中建设用地选取七大类城市建设用地的主要类型，系统在一定程度上模拟真实城市建设中的价值变动规律，其价值计算基本规则如下：</p>
                <ol style="color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    <li>公共管理与公共服务用地（类别代码为A）：回合经济收益值为100点。</li>
                    <li>商业服务设施用地（类别代码为B）：回合经济收益值为100点。</li>
                    <li>居住用地（类别代码为R）：直接经济收益值为700点。</li>
                    <li>绿地与广场用地（类别代码为B）：回合生态收益值为100点。</li>
                </ol>

                <h3 class="titlestyle" style="font-size: 15px; text-align: left;">价值变动原则</h3>
                <p style="text-indent: 2em; color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    为了更明显的反映在城市建设及城市更新过程中由于用地性质及功能不同造成的价值变动的一般规律，系统收益值以100位单位进行虚拟货币的计算。</p>
                <ol style="color: #4E5465; font-weight: bold; font-family: 楷体 ;">
                    <li>靠近绿地广场用地或交通设施用地时，其他类型用地每回合生态效益收益值加100。</li>
                    <li>靠近居住用地，由于生产用地和居住用地功能存在相互干扰，生产用地回合收益值降低100。</li>
                    <li>
                        靠近商业用地，由于商业集聚效应地块具有人流带动效应，除商业用地外各类用地回合经济收益值增加100，同类商业用地由于商业竞争经济效益的回合收益值减少100。
                    </li>
                    <li>靠近公共管理与公共服务用地或公用设施用地，由于居住配套的完善，居住用地的直接收益由700增加为800。</li>
                </ol>
                <div style="height: 100px; text-align: center;">
                    <button class="games" onclick="gameBegin()" type="button">开始游戏</button>
                </div>
                <!--   目前的游戏数据   -->
            <?php else: ?>
                <div class="titlestyle">
                    <h2>本次游戏数据报告</h2>
                </div>
                <div style="display: flex; justify-content: center; flex-wrap: wrap;">
                    <div id="chart1" style="width: 400px; height: 400px"></div>
                    <h3>更新后用地面积与初始地块面积对比图</h3>
                    <div id="chart2" style="width: 400px; height: 400px"></div>
                    <h3>规划方案地块收益对比图</h3>
                    <div style="width: 400px; height: 400px">
                        <div id="chart3" style="width: 400px; height: 400px"></div>
                    </div>
                    <div style="height: 100px; text-align: center;">
                        <button class="games" onclick="gameBegin()" type="button">再次开始游戏</button>
                    </div>
                </div>
                <script>
                    var myEchart = echarts.init(document.getElementById("chart1"));

                    var option = {
                        title: {
                            text: '方案用地面积比例分布图',
                            subtext: '',
                            left: 'center'
                        },
                        tooltip: {
                            trigger: 'item'
                        },
                        legend: {
                            orient: 'vertical',
                            left: 'left'
                        },
                        color: ['#f89588', '#eddd86', '#9987ce', '#76da91'],
                        series: [{
                            name: '个数',
                            type: 'pie',
                            radius: '50%',
                            data: [{
                                    value: <?php echo $gameStatus['land_type_1_count']; ?>,
                                    name: '公共服务'
                                },
                                {
                                    value: <?php echo $gameStatus['land_type_2_count']; ?>,
                                    name: '居住用地'
                                },
                                {
                                    value: <?php echo $gameStatus['land_type_3_count']; ?>,
                                    name: '公园绿地'
                                },
                                {
                                    value: <?php echo $gameStatus['land_type_4_count']; ?>,
                                    name: '商业设施'
                                }
                            ],
                            emphasis: {
                                itemStyle: {
                                    shadowBlur: 10,
                                    shadowOffsetX: 0,
                                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                                }
                            }
                        }]
                    };

                    myEchart.setOption(option);
                </script>
                <script>
                    var app = {};

                    var chartDom = document.getElementById('chart2');
                    var myChart = echarts.init(chartDom);
                    var option;

                    option = {
                        dataset: {
                            source: [
                                ['product', '初始面积', '当前面积'],
                                ['公共服务', 100, <?php echo $gameStatus['land_type_1_area']; ?>],
                                ['居住用地', 500, <?php echo $gameStatus['land_type_2_area']; ?>],
                                ['公园绿地', 300, <?php echo $gameStatus['land_type_3_area']; ?>],
                                ['商业设施', 800, <?php echo $gameStatus['land_type_4_area']; ?>]
                            ]
                        },
                        color: ['#79CDCD', '#7CCD7C'],
                        legend: {},
                        series: [{
                            type: 'bar'
                        }, {
                            type: 'bar'
                        }],
                        tooltip: {},
                        xAxis: {
                            type: 'category'
                        },
                        yAxis: {}
                    };

                    option && myChart.setOption(option);
                </script>
                <script>
                    var chartDom = document.getElementById('chart3');
                    var myChart = echarts.init(chartDom);
                    var option;

                    option = {
                        color: ['#80FFA5', '#00DDFF', '#37A2FF', '#FF0087', '#FFBF00'],
                        tooltip: {
                            trigger: 'axis',
                            axisPointer: {
                                type: 'cross',
                                label: {
                                    backgroundColor: '#6a7985'
                                }
                            }
                        },
                        legend: {
                            data: ['最优方案', '用户方案', '最劣方案'],
                            orient: 'horizontal'
                        },
                        toolbox: {
                            feature: {
                                saveAsImage: {}
                            }
                        },
                        grid: {
                            left: '3%',
                            right: '4%',
                            bottom: '3%',
                            containLabel: true
                        },
                        xAxis: [{
                            type: 'category',
                            boundaryGap: false,
                            data: ['公共服务', '居住用地', '公园绿地', '商业设施']
                        }],
                        yAxis: [{
                            type: 'value'
                        }],
                        series: [{
                                name: '最优方案',
                                type: 'line',
                                stack: 'Total',
                                smooth: true,
                                lineStyle: {
                                    width: 0
                                },
                                showSymbol: false,
                                areaStyle: {
                                    opacity: 0.8,
                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                            offset: 0,
                                            color: 'rgb(128, 255, 165)'
                                        },
                                        {
                                            offset: 1,
                                            color: 'rgb(1, 191, 236)'
                                        }
                                    ])
                                },
                                emphasis: {
                                    focus: 'series'
                                },
                                data: [<?php echo $gameStatus['land_type_1_count'] * 6.8; ?>, <?php echo $gameStatus['land_type_2_count'] * 5.5; ?>, <?php echo $gameStatus['land_type_3_count'] * 4; ?>, <?php echo $gameStatus['land_type_4_count'] * 5.45; ?>]
                            },
                            {
                                name: '用户方案',
                                type: 'line',
                                stack: 'Total',
                                smooth: true,
                                lineStyle: {
                                    width: 0
                                },
                                showSymbol: false,
                                areaStyle: {
                                    opacity: 0.8,
                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                            offset: 0,
                                            color: 'rgb(0, 221, 255)'
                                        },
                                        {
                                            offset: 1,
                                            color: 'rgb(77, 119, 255)'
                                        }
                                    ])
                                },
                                emphasis: {
                                    focus: 'series'
                                },
                                data: [<?php echo $gameStatus['land_type_1_count'] * 4.5; ?>, <?php echo $gameStatus['land_type_2_count'] * 2.5; ?>, <?php echo $gameStatus['land_type_3_count'] * 3; ?>, <?php echo $gameStatus['land_type_4_count'] * 3.75; ?>]
                            },
                            {
                                name: '最劣方案',
                                type: 'line',
                                stack: 'Total',
                                smooth: true,
                                lineStyle: {
                                    width: 0
                                },
                                showSymbol: false,
                                areaStyle: {
                                    opacity: 0.8,
                                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                            offset: 0,
                                            color: 'rgb(55, 162, 255)'
                                        },
                                        {
                                            offset: 1,
                                            color: 'rgb(116, 21, 219)'
                                        }
                                    ])
                                },
                                emphasis: {
                                    focus: 'series'
                                },
                                data: [<?php echo $gameStatus['land_type_1_count'] * 2.5; ?>, <?php echo $gameStatus['land_type_2_count'] * 0.5; ?>, <?php echo $gameStatus['land_type_3_count'] * 1.8; ?>, <?php echo $gameStatus['land_type_4_count'] * 2.75; ?>]
                            }
                        ]
                    };

                    option && myChart.setOption(option);
                </script>
            <?php endif; ?>
        </div>
    </main>

    <!-- 底部导航栏 -->
    <div class="footer-nav">
        <a href="javascript:void(0);" class="active" id="homeBtn" onclick="showRecentPosts()">
            <!-- 使用 Font Awesome 图标库 -->
            <i class="fa fa-home"></i>
            主页
        </a>
        <a href="javascript:void(0);" id="displayBtn" onclick="showGameInfo()">
            <i class="fa fa-gamepad"></i>
            游戏
        </a>
        <a href="javascript:void(0);" id="profileBtn" onclick="showUserInfo()">
            <i class="fa fa-user"></i>
            我的
        </a>
    </div>

    <!-- 悬浮发布新帖按钮 -->
    <div class="floating-btn" onclick="window.location.href='create_post.php';">
        <i class="fa fa-plus"></i>
    </div>

    <script>
        function gameBegin() {
            window.location.replace("./game.php");
        }
    </script>

</body>

</html>