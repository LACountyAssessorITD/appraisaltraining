
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

<form action="" method="post" enctype="multipart/form-data">
    Select databse file to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload file" name="submit">
</form>
<?php
if(isset($_POST["submit"])) {
	if ($_FILES["fileToUpload"]["name"] == "") {
		echo "Please select a file";
		return;
	}
    $target_dir = "D:/temp/";
    $FileType = pathinfo($_FILES["fileToUpload"]["name"],PATHINFO_EXTENSION);
    $target_file = $target_dir . basename("BOE".'_'.time().$FileType);
    $uploadOk = 1;


    // Check if file already exists
    if (file_exists($target_file)) {
        echo "Sorry, file already exists.";
        $uploadOk = 0;
    }
    // Check file size
    if ($_FILES["fileToUpload"]["size"] > 50000000) { // if the file is larger than 50 MB
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }
    // Allow certain file formats
    if($FileType != "mdb" && $FileType != "xlsx" && $FileType != "csv") {
        echo "Sorry, only mdb, xlsx and csv files are allowed.";
        $uploadOk = 0;
    }
    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
            echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
            echo " Renamed as ".basename("BOE".'_'.time().$FileType);
            //echo '<script>alert("File Uploaded!")</script>';
        } else {
            echo "Sorry, there was an error uploading your file.". $_FILES["fileToUpload"]['error'];
        }
    }
}
?>
<!-- <iframe name="frame"></iframe> -->

</body>
</html>


