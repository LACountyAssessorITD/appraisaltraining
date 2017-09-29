<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<?php
	include_once "php/session.php";
	
	session_start();
	checkForActiveSession();
?>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo getAppName(); ?></title>
	<!-- BOOTSTRAP STYLES-->
    <link href="assets/css/bootstrap.css" rel="stylesheet" />
    <!-- FONTAWESOME STYLES-->
    <link href="assets/css/font-awesome.css" rel="stylesheet" />
    <!-- MORRIS CHART STYLES-->
    <link href="assets/js/morris/morris-0.4.3.min.css" rel="stylesheet" />
    <!-- CUSTOM STYLES-->
    <link href="assets/css/custom.css" rel="stylesheet" />
    <!-- GOOGLE FONTS-->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
</head>
<body>
<?php
	function convertSingleQuote($str) {
		$new_str = str_replace("'", "''", $str);
		return $new_str;
	}

	function convert($str) {
		$new_str = str_replace("'", "SingleQuote", $str);
		return $new_str;
	}
?>
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-cls-top " role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".sidebar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php">
                <h5><?php echo getUserName(); ?></h5>
                </a> 
            </div>
			<div style="color: white; padding: 15px 50px 5px 50px; float: right;font-size: 16px;">  
				<a href="php/process_logout.php" class="btn btn-danger square-btn-adjust">Logout</a> 
			</div>
        </nav>   
        <!-- /. NAV TOP  -->
        <nav class="navbar-default navbar-side" role="navigation">
            <div class="sidebar-collapse">
                <ul class="nav" id="main-menu">
			<li class="text-center">
				<img src="assets/img/Assessor.jpg" class="user-image img-responsive"/>
			</li>	
                    	
			<?php if ($_SESSION["ROLE"] != 3) { ?>
				<li>
					<a class="active-menu" href="index.php"><i class="fa fa-dashboard fa-3x"></i> Dashboard</a>
				</li>   
			
			<?php }else {  ?>
				<li>
					<a class="active-menu" href="reports.php"><i class="fa fa-reports fa-3x"></i> Reports</a>
				</li>  
			 <?php } ?>
			<?php if ($_SESSION["ROLE"] != 3) { //Not Reports ?>
				<li>
					<a href="form.php"><i class="fa fa-edit fa-3x"></i> Create</a>
				</li>   
			<?php } ?>
                    	<li>
                        	<a href="search.php"><i class="fa fa-sitemap fa-3x"></i> Search</a>
                    	</li> 
                    	<li>
                        	<a href="table.php"><i class="fa fa-table fa-3x"></i> All Assets</a>
                    	</li>
			<?php if ($_SESSION["ROLE"] != 3) { //Not Reports ?>
			<li>
				<a href="mobilescan.php"><i class="fa fa-mobile fa-3x" style="padding-left: 10px; padding-right: 10px"></i> mobile scan</a>
			</li>
			<?php } ?>
                </ul>            
            </div>
        </nav>  
        <!-- /. NAV SIDE  -->
        <div id="page-wrapper" >
            <div id="page-inner">
                <div class="row">
                    <div class="col-md-12">
						<h2>Management Services</h2>   
                        <h5>Welcome <?php echo getUserName(); ?></h5>
                    </div>
                </div>              
                <!-- /. ROW  -->
                <hr />
                <div class="row">
					<div class="col-md-4 col-sm-4 col-xs-12">           
						<div class="panel panel-back noti-box">
							<span class="icon-box bg-color-red set-icon">
								<i class="fa fa-desktop"></i>
							</span>
							<div class="text-box" >
								<p class="main-text"> 
								<?php			
								$servername = SQL_SERVER_NAME;
								$username = SQL_SERVER_USERNAME;
								$password = SQL_SERVER_PASSWORD;
								$dbname = SQL_SERVER_DATABASE;
								
								$connectionInfo = array("UID"=>$username, "PWD"=>$password, "Database"=>$dbname);
								$conn = sqlsrv_connect($servername, $connectionInfo);
								if($conn === false) {
									echo "Connection could not be established.";
									die(print_r(sqlsrv_errors(), true));
								}
								
								$sql = "select * from All_Assets where CustodianID = " . $_SESSION["USERNAME"] . ";";
								$params = array();
								$options =  array("Scrollable" => SQLSRV_CURSOR_KEYSET);
								$stmt = sqlsrv_query($conn, $sql, $params, $options);
								if($stmt === false) {
									die(print_r(sqlsrv_errors(), true));
								}
								
								$row_count = sqlsrv_num_rows($stmt);
								if ($row_count === false)
									echo "Error in retrieveing row count.";
								else
									echo $row_count;
								sqlsrv_free_stmt($stmt);
								?>
								Device</p>
								<p class="text-muted">Under My Name</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">           
						<div class="panel panel-back noti-box">
							<span class="icon-box bg-color-blue set-icon">
								<i class="fa fa-bell-o"></i>
							</span>
							<div class="text-box" >
								<p class="main-text">

								<?php 
								if ($_SESSION["ROLE"] == 1) { //Administrator
									$sql = "select * from All_Assets where Pending = 1;";
								}
								else { //Technician, Reports
									$sql = "select * from All_Assets where Pending = 1 and UpdateUserID = " . $_SESSION["USERNAME"] . ";";
								}
								$params = array();
								$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
								$stmt = sqlsrv_query($conn, $sql, $params, $options);
								if($stmt === false) {
									die(print_r(sqlsrv_errors(), true));
								}
													
								$row_count = sqlsrv_num_rows($stmt);
								if ($row_count === false)
									echo "Error in retrieveing row count.";
								else
									echo $row_count;
								sqlsrv_free_stmt($stmt);
								?>
								New</p>
								<p class="text-muted">Requests</p>
							</div>
						</div>
					</div>
					<div class="col-md-4 col-sm-4 col-xs-12">           
						<div class="panel panel-back noti-box">
							<span class="icon-box bg-color-green set-icon">
								<i class="fa fa-bars"></i>
							</span>
							<div class="text-box" >
								<p class="main-text">10 Recent</p>
								<p class="text-muted">Transaction(s)</p>
							</div>
						</div>
					</div>
				</div>
				<!-- end of row -->
                <!-- /. ROW  -->
                <div class="row" >
                    <div class="col-md-12 col-sm-12 col-xs-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-desktop"></i>
								Devices Under My Name
							</div>
							<div class="panel-body">
								<div class="table-responsive">
									<table class="table table-striped table-bordered table-hover">
										<thead>
											<tr>
												<th>Asset #</th>
												<th>Make</th>
												<th>Model</th>
												<th>Description</th>
												<th>Location</th>
											</tr>
										</thead>
										</tbody>
										<?php		
										$sql = "select * from All_Assets where CustodianID = " . $_SESSION["USERNAME"] . " order by AssetTag;";										
										$params = array();
										$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
										$stmt = sqlsrv_query($conn, $sql, $params, $options);
										if($stmt === false) {
											die(print_r(sqlsrv_errors(), true));
										}

										$row_count = sqlsrv_num_rows($stmt);
										if ($row_count === false)
											echo "Error in retrieveing row count.";
										else {
											if ($row_count > 0) {
												while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
													echo '<tr>';
													echo '<td>', $row['AssetTag'], '</td>';
													echo '<td>', $row['Make'], '</td>';
													echo '<td>', $row['Model'], '</td>';
													echo '<td>', $row['Descr'], '</td>';
													echo '<td>', $row['LocationQRCode'], '</td>';
													echo '</tr>';
												}
											}
										}
										sqlsrv_free_stmt($stmt);
										?>
									</table>
								</div>
							</div>
						</div>
                    </div>
                </div>
                <!-- end of row -->
				<!-- /. ROW  -->
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bell fa-fw"></i>
								New Requests               
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordion">
<!-- //////////////////////////////////////// -->

<?php			  
    if ($_SESSION["ROLE"] == 1) { //Administrator
		$sql = "select * from All_Assets where Pending = 1 order by AssetTag;";
	}
	else { //Technician, Reports
		$sql = "select * from All_Assets where Pending = 1 and UpdateUserID = " . $_SESSION["USERNAME"] . " order by AssetTag;";
	}
	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$stmt = sqlsrv_query($conn, $sql, $params, $options);
	if($stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$row_count = sqlsrv_num_rows($stmt);
	if ($row_count === false)
   		echo "Error in retrieveing row count.";
	else {
		if ($row_count > 0) {
			while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
 				echo '<div class="panel panel-default" id = "DIV', $row['AssetID'],'">
      		        		<div class="panel-heading">
                                        <h2 class="panel-title">
                                       	<a data-toggle="collapse" data-parent="#accordion" href="#';
                                            echo $row['AssetID'];
                                            echo '" class="collapsed">';
                                            echo "Asset # ", $row['AssetTag'];
                                            echo '</a>
                                      	</h2>
                                   	</div>
                                   	<div id="';
                echo $row['AssetID'];
                echo'" class="panel-collapse in" style="height: auto;"><div class="panel-body">';
				
				if ($row['AuditStatusID'] == "2") {
					echo "Request: Asset # " . $row['AssetTag'] . " is being assigned to Custodian ID " . $row['CustodianID'] . " (" . $row['CustodianName'] . ")";}
				else if ($row['AuditStatusID'] == "3") {
					echo "Request: Asset # " . $row['AssetTag'] . " is being assigned to Location QR Code " . $row['LocationQRCode'];}
				else if ($row['AuditStatusID'] == "4") {
					echo "Request: Asset # " . $row['AssetTag'] . " is being assigned to the Custodian ID " . $row['CustodianID'] . " (" . $row['CustodianName'] . ") and the Location QR Code " . $row['LocationQRCode'];}
							  
				if ($_SESSION["ROLE"] == 1) { //Administrator	
					echo '<br>
							<button type="button" class="btn btn-warning btn-circle btn-lg" style="float: right; margin-left: 10px;" title = "Deny" onclick="disapprove(\'', $row['AssetID'], '\')">
							<i class="glyphicon glyphicon-remove "></i></button>   
							<button type="button" class="btn btn-info btn-circle btn-lg" style="float: right; display: true;" title = "Approve" onclick="approve(\'', $row['AssetID'], '\')";>

							<i class="glyphicon glyphicon-ok"></i></button>
							<br style="clear: both;">';
				}
				echo '</div></div></div>';

			}
		}
	}
	sqlsrv_free_stmt($stmt);
?> 
	<script type="text/javascript">
        function approve(AssetID) {
            //console.log("hello");
            //console.log(object);
            //return;
            $.ajax({
                type: "POST",
                url: 'php/approvePending.php',
                data:{ 
                    "id": AssetID
                },
                success:function(html) {
                    //alert(html);
                }

			});
            
			$("#DIV"+AssetID).remove();
			window.location.reload(true);
        }

        function disapprove(AssetID) {
            //alert("dis");
            //document.querySelector("#"+object).remove();
            $.ajax({
                type: "POST",
                url: 'php/denyPending.php',
                data:{ 
                    "id": AssetID
                },
                success:function(html) {
                    //alert(html);
                }

            });
			
			$("#DIV"+AssetID).remove();
			window.location.reload(true);
        }
    </script>                             

<!-- //////////////////////////////////////// -->                   
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /. ROW  -->
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-default">
							<div class="panel-heading">
								<i class="fa fa-bars fa-fw"></i>
								Recent Transaction(s)              
							</div>
							<div class="panel-body">
								<div class="panel-group" id="accordion1">
<!-- //////////////////////////////////////// -->

<?php			  
 
	$sql = "select a.LocationQRCode from All_Assets a inner join ";
	$sql .= "(select top 10 * from  History order by CreateDate Desc) t on a.AssetID = t.AssetID ";
	$sql .= "group by a.LocationQRCode order by a.LocationQRCode;";

	$params = array();
	$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
	$stmt = sqlsrv_query($conn, $sql, $params, $options);
	if($stmt === false) {
		die(print_r(sqlsrv_errors(), true));
	}

	$row_count = sqlsrv_num_rows($stmt);
	if ($row_count === false)
   		echo "Error in retrieveing row count.";
	else {
		if ($row_count > 0) {
			while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
 				echo '<div class="panel panel-default" id = "DIV', $row['LocationQRCode'],'">
      		        		<div class="panel-heading">
                                        <h2 class="panel-title">
                                       	<a data-toggle="collapse" data-parent="#accordion1" href="#';
                                            echo convert($row['LocationQRCode']);
                                            echo '" class="collapsed">';
                                            echo '<strong>' . 'Location ' . $row['LocationQRCode'] . '</strong>';
                                            echo '</a>
                                      	</h2>
                                   	</div>
                                   	<div id="';
                echo convert($row['LocationQRCode']);
                echo'" class="panel-collapse in" style="height: auto;"><div class="panel-body">';
				
				$sql1 = "select t.Description, t.CreateDate from All_Assets a inner join ";
				$sql1 .= "(select top 10 * from  History order by CreateDate Desc) t on a.AssetID = t.AssetID ";
				$sql1 .= "where a.LocationQRCode = '" . convertSingleQuote($row['LocationQRCode']) . "';";
				$params1 = array();
				$options1 = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
				$stmt1 = sqlsrv_query($conn, $sql1, $params1, $options1);
				if($stmt1 === false) {
					die(print_r(sqlsrv_errors(), true));
				}

				$row_count1 = sqlsrv_num_rows($stmt1);
				if ($row_count1 === false)
					echo "Error in retrieveing row count.";
				else {
					if ($row_count1 > 0) {
						while($row1 = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
							echo $row1['Description'];
							echo ' <small class="pull-right text-muted"><i class="fa fa-clock-o fa-fw"></i>';
							echo date_format($row1['CreateDate'], 'Y-m-d H:i:s');
							echo '</small>' . '<br>';
						}
					}
				}
				echo '</div></div></div>';
			}
			sqlsrv_free_stmt($stmt1);
		}
	}
	sqlsrv_free_stmt($stmt);
	sqlsrv_close($conn);
?>    
								</div>
							</div>
						</div>
					</div>    
    <!-- /. ROW  -->
				</div> 
    <!-- /. PAGE INNER  -->
			</div>
    <!-- /. PAGE WRAPPER  -->
		</div>
    <!-- /. WRAPPER  -->
	</div>
    <!-- SCRIPTS -AT THE BOTOM TO REDUCE THE LOAD TIME-->
    <!-- JQUERY SCRIPTS -->
    <script src="assets/js/jquery-1.10.2.js"></script>
    <!-- BOOTSTRAP SCRIPTS -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- METISMENU SCRIPTS -->
    <script src="assets/js/jquery.metisMenu.js"></script>
    <!-- MORRIS CHART SCRIPTS -->
    <script src="assets/js/morris/raphael-2.1.0.min.js"></script>
    <script src="assets/js/morris/morris.js"></script>
    <!-- CUSTOM SCRIPTS -->
    <script src="assets/js/custom.js"></script>
</body>
</html>
