<?php
	include_once "session.php";
	session_start();
?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="lib/jquery/jquery-3.1.1.min.js"></script>
	<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
	<script type="text/javascript">
		var year = 2016;
		var total = 0;
		var curr = 0;
		function startsending() {
			curr = 0;
			total = 0;
			 $.ajax({
				url:"emailall.php", //the page containing php script
				//headers: {'Content-Type': 'application/json'},
				type: "POST", //request type
				dataType: "json",
				success:function(results){
					total = results.length;
					for (var i = 0; i < 20; i++) { // should be total. FOr testing it is 20
						var id = results[i];
						send(id,year);
					}
				},
				error: function(xhr, status, error){
			    	//alert(xhr.responseText);
			    }
			});
		};
		function moveProgress(){
			var per = curr * 100 / total;
			//alert(per);
			document.querySelector("#progressBar").style.width = per * 2 +"px";
		};
		function send(id,year) {
			 $.ajax({
			 	url:"generatePDFandemail.php", //the page containing php script
				type: "POST", //request type
				data: {id:id, year:year},
				success:function(){
					curr ++;
					moveProgress();
				},
				error: function(xhr, status, error){
			    	//alert(xhr.responseText);
			    }
			});
		};

	</script>
	<style type="text/css">
		#progressContainer{
		  margin-top: 100px;
		  margin-right: auto;
		  margin-left: auto;
		  width: 200px;
		  background-color: black;
		  height: 20px;
		}
		#progressBar{
		  width: 0%;
		  height: 20px;
		  background-color: red;
		}
	</style>
</head>

<body>
<div id="progressbar" style="border:1px solid #ccc; border-radius: 5px; "></div>
<div id = "progressContainer"><div id = "progressBar"></div></div>
<button type="button" onclick="startsending()" id="button1">Click Here To Start Sending Emails</button>









<a href="communicator.php?download=YES" target="_blank">
	<button>Download Report</button>
</a>


<form method="post" action="communicator.php" method="post">
    <input type="hidden" name="download" value="YES" />
    <input type="submit" value="Go" />
</form>









<div id="pdf">
	<iframe src="pdf.php"  width="1200" height="1000" frameborder="0" scrolling="no">
		<p>It appears your web browser doesn't support iframes.</p>
	</iframe>
</div>

<a target="popup" onclick="window.open('', 'popup', 'width=580,height=360,scrollbars=no, toolbar=no,status=no,resizable=yes,menubar=no,location=no,directories=no,top=10,left=10')" href="admin_email.php?mail=foo@example.com">foo@example.com</a>

</body>
</html>


