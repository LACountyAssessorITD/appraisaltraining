
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="lib/jquery/jquery-3.1.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script>
  //   	$('#submit').click(function() {
		//     $.ajax({
		//         url: 'upload.php',
		//         type: 'POST',
		//         data: {
		//             email: 'email@example.com',
		//             message: 'hello world!'
		//         },
		//         success: function(msg) {
		//             alert('Email Sent');
		//         }               
		//     });
		// });
    </script>
</head>

<body>

<iframe name="frame" style="display:none;"></iframe>
<form target="frame" action="upload.php" method="post" enctype="multipart/form-data">
    Select databse file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload file" name="submit">
</form>

</body>
</html>


