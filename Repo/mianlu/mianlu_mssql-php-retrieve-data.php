<html>
<head>
<title>MSSQL PHP Example -- Table List</title>
</head>
<body>
<?php
// This script will list all the tables in the specified data source.
// Replace datasource_name with the name of your data source.
// Replace database_username and database_password
// with the SQL Server database username and password.

$data_source='Assessor';
$user='superadmin';
$password='admin';
/*
// ===============================================================================
// This Code dynamically generate PDFs and send the PDF as attachment to GMAIL
// @ Yining Huang
include_once 'lib/PHPMailer/src/Exception.php';
include_once 'lib/PHPMailer/src/PHPMailer.php';
include_once 'lib/PHPMailer/src/SMTP.php';
include_once "constants.php";
session_start();
// Access Database here 
$serverName = SQL_SERVER_NAME; 
$uid = SQL_SERVER_USERNAME;
$pwd = SQL_SERVER_PASSWORD;
$db = SQL_SERVER_BOEDATABASE;
$connectionInfo = array( "UID"=>$uid,  
                         "PWD"=>$pwd,  
                         "Database"=>$db,
             "ReturnDatesAsStrings"=>true);  // convert datetime to string
             
// Connect using SQL Server Authentication.
$conn = sqlsrv_connect( $serverName, $connectionInfo);  
if( $conn === false )  
{  
     echo "Unable to connect.</br>";  
     die( print_r( sqlsrv_errors(), true));  
}
// ===============================================================================
*/
// Connect to the data source and get a handle for that connection.
$conn=odbc_connect($data_source,$user,$password);
if (!$conn){
    if (phpversion() < '4.0'){
      exit("Connection Failed: . $php_errormsg" );
    }
    else{
      exit("Connection Failed:" . odbc_errormsg() );
    }
}

// Retrieves table list.
$result = odbc_tables($conn);

   $tables = array();
   while (odbc_fetch_row($result))
     array_push($tables, odbc_result($result, "TABLE_NAME") );
// Begin table of names.
     echo "<center> <table border = 1>";
     echo "<tr><th>Table Count</th><th>Table Name</th></tr>";
// Create table rows with data.
   foreach( $tables as $tablename ) {
     $tablecount = $tablecount+1;
     echo "<tr><td>$tablecount</td><td>$tablename</td></tr>";
   }

// End table.
echo "</table></center>";
// Disconnect the database from the database handle.
odbc_close($conn);

/*
# connect to a DSN "mydb" with a user and password "marin" 
$conn = odbc_connect(blah blah blah)

# query the users table for name and surname
$query = "SELECT LastName, FirstName FROM users";

# perform the query
$result = odbc_exec($connect, $query);

# fetch the data from the database
while(odbc_fetch_row($result)){
  $name = odbc_result($result, 1);
  $surname = odbc_result($result, 2);
  print("$name $surname\n");
}

# close the connection
odbc_close($connect);
?>
*/


?>
</body>
</html>

