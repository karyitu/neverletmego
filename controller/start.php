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

if (!empty($_POST["controller_name"])) {

    $sql = "INSERT INTO Controllers (controller_data) VALUES ('". $_POST["controller_name"] ."')";
    
 
  //echo $sql;
    if ($conn->query($sql) === TRUE) {
        //echo "New record created successfully<br><br>";        
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
  }  
     
$sql = "SELECT * FROM Controllers ORDER BY created_at DESC";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
    
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CONTROLLER</title>
        <link rel="stylesheet" href="main.css">
          <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
   </head>
    <body>
        <div class="title">CONTROLLER</div>
        <div class="controller">
            <form id="start" action="start.php" method="post">
                Name your controller:<br>
                <input name="controller_name" type="text">
                <input type="submit" class="button" value="REGISTER">
            </form>
            <br><br>
            <b>Or pick controller:</b><br>
            <table>
<?php
      if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
             if (!empty($row["controller_data"])){
                  $date= new DateTime($row["created_at"]);  
                  echo "<tr><td><a href='get_devices.php?controller_id=" . $row["controller_id"]  . "'>" . $row["controller_data"] . "</a></td><td>" . $date->format('Y-m-d') . "</td></tr>";
                  }
                  
            }
    }

    $conn->close(); 
?>
        </table>
        </div>
    </body>
</html>
