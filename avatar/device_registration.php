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

echo '{"servermsg":"';

if (!empty($_GET["device_token"])) {
    $sql = "INSERT INTO Subscriptions (device_token)
    VALUES ('". $_GET["device_token"] ."')";

    //echo $sql;
    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
else
    echo "No device_token provided!";

echo '"}';
$conn->close(); 


?>