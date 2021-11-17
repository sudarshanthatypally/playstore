<?php
/* Attempt MySQL server connection. Assuming you are running MySQL
server with default setting (user 'root' with no password) */
$db_server = "epiko-db.cgkf0qydg6ah.eu-west-2.rds.amazonaws.com";
$db_username = "db_admin";
$db_password = "TtKQRrorDKAp5cK0eczG";
$db_name = "playstore";

$mysqli = new mysqli($db_server, $db_username, $db_password, $db_name);
 
$id=$_GET['id'];
// Check connection
if($mysqli === false){
    die("ERROR: Could not connect. " . $mysqli->connect_error);
}
 
// Attempt update query execution
$sql = "SELECT facebook_id FROM user WHERE facebook_id=".$id;
if($result = $mysqli->query($sql)){
    if($result->num_rows > 0){
        echo json_encode("Data not Deleted");
    }else{
        echo json_encode("Records were updated successfully.");
    }
}
 
// Close connection
$mysqli->close();
?>
