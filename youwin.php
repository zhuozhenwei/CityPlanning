<!DOCTYPE html>
<html lang="en">
    <?php
        session_start();
        if(!isset($_SESSION['username'])){
            header("Location: login.php");
            exit();
        }
    ?>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>胜利页面</title>
    <style>
        .footer{
            bottom: 0;
            position: absolute;
            text-align: center;
            width: 85%;
            line-height: auto;
            position: fixed;
        }
        .main{
            text-align: center;
            min-height: 800px;
            background-image: url("图片/bg.png");
            background-position-y: 300px;
            background-repeat: no-repeat;
        }
        #logo{
            width: 50%;
            height: 50%;
        }
        .games{
            width: 150px;
            height: 50px;
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
            margin-left: 15%;
            margin-right: 15%;
        }
        #ret{
            width: 75px;
            height: 50px;
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
            margin-left: 15%;
            margin-right: 15%;
        }
    </style>
</head>
<body>
    <div class="main">
        <div style="text-align: center;margin: 0;">
            <img src="./图片/GZB618ZEJ$GRWK{IGX94{2E.jpg" alt="" id="logo">
        </div>
        <h1 style="font-size: 3em; margin-top: -50px;">胜&nbsp;&nbsp;利</h1>
        <a style="font-size: 3em;">当前得分：<?php echo $_SESSION['score'] ?? '0'; ?></a>
        
        <form>
            <td>
                <br>
                <th>
                    <br>
                    <button class="games" onclick="again()" type="button">
                        <a style="font-weight: bolder;font-family: '千绘图文','幼圆';font-size: larger;">再来一局</a>
                    </button>
                </th>
                <br>
                <th>
                    <br>
                    <button id="ret" onclick="returnmain()" type="button">
                        <a style="font-weight: bolder;font-family: '千绘图文','幼圆';font-size: larger;">返回</a>
                    </button>
                </th>
            </td>
        </form>
    </div>

    <script>
        function again() {
            window.location.replace("./game.php");
        }
        function returnmain() {
            window.location.replace("./index.php");
        }
    </script>
</body>
</html>