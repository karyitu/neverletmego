<?php

$servername = "localhost";
$username = "yourusername";
$password = "yourpassword";
$dbname = "yourdatabasename";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

$output = '{"reslut":"';

if (!empty($_GET["comid"])) {

    $sql = "UPDATE Commands SET played=TRUE WHERE command_id =". $_GET["comid"];
    if ($conn->query($sql) === TRUE) {
        //echo "New record created successfully";
        $output = $output . 'ok';
    } else {
        //echo "Error: " . $sql . "<br>" . $conn->error;
        $output = $output . 'error';
    }
}
     
$output = $output . '"}';
$conn->close(); 


echo $output;

?>


