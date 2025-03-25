<!doctype html>
<html>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<title>别作弊了 宝</title>
<style>
	body{
		background-color:#444;
		font-size:14px;
	}
	h3{
		font-size:60px;
		color:#eee;
		text-align:center;
		padding-top:30px;
		font-weight:normal;
	}
</style>
</head>

<body>
<br />
<h3>我们说 这就是经典的错误 标准的零分</h3>
<br/>
<?php 
    session_start();
    echo "<a>".$_SESSION['Round']."</a>";
?>
</body>
</html>
