<?php

$servername = "localhost";
$username = "yourusername";
$password = "yourpassword";
$dbname = "yourdatabasename";

$controller_id = $_GET["controller_id"];
if ($controller_id=="")
    $controller_id = $_POST["controller_id"];

if (!empty($_POST["device_token"])) {
    
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    } 


    $sql = "SELECT * FROM Subscriptions WHERE device_token LIKE '" . $_POST["device_token"] . "%'";
    $result = $conn->query($sql);

}

?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CONTROLLER</title>
        <link rel="stylesheet" href="main.css">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script>
            function validateForm() {
              var str = document.forms["search"]["device_token"].value;
              var n = str.length;
              if (n < 10) {
                alert("The code needs to be 10 digits long!");
                return false;
              }
            }
        </script>
    </head>
    <body>
        <div id="title" class="title">FIND YOUR AVATAR:</div>
        <div class="controller">
            <br>
        <form id="search" action="get_devices.php" method="post" onsubmit="return validateForm()">
                10 digit code:<br>
                <input name="device_token" type="text" max="10" min="10">
                <input type="submit" class="button" value="SEARCH">
                <input type="hidden" name="controller_id" value="<?php echo $controller_id ?>">
            </form>
            <br>
        <table>
<?php
if (!empty($_POST["device_token"])) {            
     if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                 if (!empty($row["device_token"])){
                      echo "<tr><td width='50%' style='word-wrap: break-word'><a href='index.php?avatar_id=" . $row["avatar_id"]  . "&controller_id=". $controller_id . "'>" . substr($row["device_token"],0,10) . "</a></td><td>" . $row["created_at"] . "</td></tr>";
                      }

                }
        }
        else
            echo "<tr><td>None was found. Try again!</td></tr>";

        $conn->close(); 
    }
?>
        </table> 
        </div>
    </body>
</html>