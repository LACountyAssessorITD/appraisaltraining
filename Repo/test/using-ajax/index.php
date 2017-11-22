<?php
// Start the session.
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Progress Bar</title>
  <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
   <script>
    var timer;
    // The function to refresh the progress bar.
    function refreshProgress() {
    //   alert(".");
      // $("#hasStarted")[0].innerHTML = "yes";
      // We use Ajax again to check the progress by calling the checker script.
      // Also pass the session id to read the file because the file which storing the progress is placed in a file per session.
      // If the call was success, display the progress bar.

      var urlStr = "checker.php?file=<?php echo session_id() ?>";
      $.ajax({
        cache: false,
        url: urlStr,
        success:function(data){
          // $("#progress").html('<div class="bar" style="width:' + data.percent + '%"></div>');
          // alert(data.percent);
          $("#progress").css("width",data.percent+"%");
          $("#message").html(data.message);
          // If the process is completed, we should stop the checking process.
          if (data.percent == 100) {
            alert('done');
            window.clearInterval(timer);
            timer = window.setInterval(completed, 1000);
          }
        }
      });
    }

    function completed() {
      $("#message").html("Completed");
      window.clearInterval(timer);
    }
    function start() {
      // Trigger the process in web server.
       $.ajax({url: "process.php"});
      // Refresh the progress bar every 1 second.
      timer = window.setInterval(refreshProgress, 1000);
    }


    // When the document is ready
    $(document).ready(function(){
      // // Trigger the process in web server.
      // $.ajax({url: "process.php"});
      // // Refresh the progress bar every 1 second.
      // timer = window.setInterval(refreshProgress, 1000);
    });

    // $(document).on("click","#startBtn", start);


  </script>
  <style>
    #progress {
      /*width: 500px;*/
      width: 1%;
      border: 1px solid #aaa;
      height: 20px;
    }
    /*#progress .bar {
      background-color: #ccc;
      height: 20px;
    }*/
  </style>
</head>
<body>
  <label id='hasStarted'>no</label>
  <div id="progress"></div>
  <div id="message"></div>
   <button onclick="start()">Start</button>

</body>
</html>
