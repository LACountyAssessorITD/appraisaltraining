<html>
	<?php
	session_start();
	//$_SESSION["id"] = $_POST['id'];
	//$_SESSION["year"] = $_POST['year'];
	$_SESSION["id"] = 11619;
	$_SESSION["year"] = 2016;
	?>
<body>
	
	<object data="singleUserReport.php" type="application/pdf"  width="1200" height="1000">
		<p>It appears you don't have Adobe Reader or PDF support in this web browser. 
		<a href="singleUserReport.php">Click here to download the PDF</a>. Or 
		<a href="http://get.adobe.com/reader/" target="_blank">click here to install Adobe Reader</a>.</p>
		<embed src="singleUserReport.php" type="application/pdf"  width="1200" height="1000"/>
	</object>

</body>
</html>