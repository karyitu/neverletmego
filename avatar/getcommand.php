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

$output = '{"command":"';

if (!empty($_GET["device_token"])) {

    $sql = "SELECT avatar_id FROM Subscriptions WHERE device_token='". $_GET["device_token"] ."'";
    $result = $conn->query($sql);
    
     if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
              if (!empty($row["avatar_id"]))
                    $sql2 = "SELECT command_id, command, created_at FROM Commands WHERE avatar_id=". $row["avatar_id"] ." AND played=FALSE ORDER BY created_at DESC LIMIT 1";
            }
    
    }
    
    $result2 = $conn->query($sql2);

    if ($result2->num_rows > 0) {

            while($row2 = $result2->fetch_assoc()) {
              if (!empty($row2["command"]))
                    $output = $output . $row2["command"] . '", "created_at":"' . $row2["created_at"] . '", "command_id":"' . $row2["command_id"];
            }
    }
 }
     
$output = $output . '"}';
$conn->close(); 


echo $output;

?>


