<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>You lose</title>
    <style>
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

        .main {
            text-align: center;
            min-height: 800px;
            background-image: url("图片/bg.png");
            background-size: 100% 80%;
            background-position-y: 300px;
            background-repeat: no-repeat;
            position: relative;
        }

        #logo {
            width: 50%;
            height: 50%;
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
    </style>
</head>
<body>

<div class="main">
    <div style="text-align: center;margin: 0;">
        <img src="./图片/GZB618ZEJ$GRWK{IGX94{2E.jpg" alt="" id="logo">
    </div>
    <h1 style="font-size: 3em; margin-top: -50px;">败&nbsp;&nbsp;北</h1>
    <form>
        <td>
            <br>
        <th>
            <br>
            <button class="games" onclick="again()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                        font-size: larger;">再来一局</a></button>
        </th>
        <br>
        <th>
            <br>
            <button id="ret" onclick="returnmain()" type="button"><a style="font-weight: bolder;font-family: '千绘图文','幼圆';
                        font-size: larger;">返回</a></button>
        </th>
        </td>
    </form>
</div>

<div class="footer" style="text-align: center;width: 100%;background-color: white;">
    <table style="text-align: center;width: inherit;">
        <tr>

        </tr>
    </table>
</div>

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

    function again() {
        window.location.replace("./game.php");
    }

    function returnmain() {
        window.location.replace("./index.php");
    }
</script>
</body>
</html>