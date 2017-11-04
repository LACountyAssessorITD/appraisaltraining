<?php
	session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Training Record Login</title>
      <style>
body{
	margin: 0;
	padding: 0;
	background: #fff;

	color: #fff;
	font-family: Arial;
	font-size: 12px;
}

.body{
	position: absolute;
	top: -20px;
	left: -20px;
	right: -40px;
	bottom: -40px;
	/*width: auto;
	height: auto;*/
	background-image: url(BGimg/LAbgAdj.jpg);
	background-size: cover;
	-webkit-filter: blur(5px);
	z-index: 0;
}

.grad{
	position: absolute;
	top: -20px;
	left: -20px;
	right: -40px;
	bottom: -40px;
	width: auto;
	height: auto;
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(0,0,0,0)), color-stop(100%,rgba(0,0,0,0.65))); /* Chrome,Safari4+ */
	z-index: 1;
	opacity: 0.7;
}

.header{
	margin: 0 auto;
	position: absolute;
	top: calc(25%);
	left: auto;
	z-index: 2;
	right: calc(35%);
}

.header div{
	width: auto;
	right: -40px;
	float: left;
	color: #fff;
	font-family: 'Exo', sans-serif;
	font-size: 35px;
	font-weight: 200;
	font-weight: bold;

}

.header div span{
	color: #56BDFC !important;
	font-weight: bold;
}

.messageDiv {
	margin: 0 auto;
	padding: 10px;
	width: 100%;
	background-color: black;
    opacity: 0.5;
    filter: Alpha(opacity=50);  /*IE8 and earlier*/ 
    border-radius: 10px;
}


</style>

</head>

<body>
	<div class="body"></div>
	<div class="grad"></div>
	<div class="header">
		<div class="messageDiv">
			<div>Sorry. <span>	An Error occured.</span></div>
		</div>
	</div>
	<br>
</body>
</html>
