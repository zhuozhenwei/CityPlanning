<!DOCTYPE html>
<html lang="en">
<?php
session_start();
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = '';
}
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./layui/css/layui.css" media="all">
    <script>
        var $id = "<?php session_start(); echo $_SESSION['username']; ?>";
        if ($id == "") {
            alert("您还没有登录");
            window.location.replace("./index.php");
        }
    </script>
    <script src="./layui/layui.js"></script>
    <title>ChooseGame</title>
    <style>
        #logo {
            width: 50%;
            height: 50%;
        }

        #bg {
            height: 50%;
            width: 100%;
        }

        #login {
            width: 150px;
            height: 50px;
            background-position-y: 50px;
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
        }

        #maind {
            background: url("./图片/bg.png") no-repeat 3px center;
            height: 500px;
            background-size: 100% 100%;
        }

        .games {
            width: 150px;
            height: 50px;
            /* background-position-y: 50px; */
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
            margin-left: 15%;
            margin-right: 15%;
        }

        #ret {
            width: 75px;
            height: 50px;
            /* background-position-y: 50px; */
            border: 1px;
            border-radius: 15px;
            border-style: solid;
            font-weight: bolder;
            margin-left: 15%;
            margin-right: 15%;
        }

        .footer {
            bottom: 0;
            position: absolute;
            text-align: center;
            width: 85%;
            line-height: auto;
            position: fixed;
        }

        .imgs {
            height: 35px;
            width: 35px;
        }

    </style>
</head>

<body>
<div style="text-align: center;">
    <img src="./图片/GZB618ZEJ$GRWK{IGX94{2E.jpg" alt="" id="logo">
</div>
<div style="text-align: center;" style="width: 80%;height: 1000px;" id="maind">
    <!-- <img src="./图片/bg.png" alt="" id="bg"> -->
    <form>
        <th>
            <br>
            <button class="games" onclick="single()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                            font-size: larger;">单人模式</a></button>
        </th>
        <br>
        <th>
            <br>
            <button class="games" onclick="single()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                            font-size: larger;">协同模式</a></button>
        </th>
        <br>
        <th>
            <br>
            <button class="games" onclick="single()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                            font-size: larger;">博弈模式</a></button>
        </th>
        <br>
        <th>
            <br>
            <button id="ret" onclick="returnmain()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                            font-size: larger;" >返回</a></button>
        </th>
    </form>
</div>
<!--<div class="footer" style="text-align: center;width: 100%;background-color: white;">-->
<!--    <table style="text-align: center;width: inherit;">-->
<!--        <tr>-->
<!---->
<!--        </tr>-->
<!--    </table>-->
<!--</div>-->

<script>
    function left() {
        window.location.replace("./user.php");
    }

    function mid() {
        window.location.replace("./luntan.php");
    }

    function right() {
        window.location.replace("./game.php");
    }

    function single() {
        window.location.replace("./playsingle.php");
    }

    function returnmain() {
        window.location.replace("./index.php");
    }
</script>


<script>
    function login() {
        window.location.replace("./index.php");
    }
</script>
</body>

</html>