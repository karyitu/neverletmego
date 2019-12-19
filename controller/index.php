<?php

$sentOK = 0;
$duration = 1;
$bgcolor = "#fabd03";
$totalSeconds = 0;
$active_tab = 0;
$command = "";
$error = 0;

//if command has been submitted

if (!empty($_POST["command"])) {
    
    $active_tab = $_POST["active_tab"];
    $duration = $_POST["duration"];
    $bgcolor = $_POST["bgcolor"];
    $totalSeconds = $_POST["totalSeconds"];
    $command = $_POST["command"];
    
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

    $sql = "INSERT INTO Commands (controller_id, avatar_id, command) VALUES (".$_POST["controller_id"] . "," . $_POST["avatar_id"] .",'" . $_POST["command"] . "')";

     
    if ($conn->query($sql) === TRUE) {
        //echo "New record created successfully";
    } else {
        //echo "Error: " . $sql . "<br>" . $conn->error;
        $error = 1;
    }
   

    $sql = "SELECT device_token FROM Subscriptions WHERE avatar_id=" . $_POST["avatar_id"];
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        while($row = $result->fetch_assoc()) {
            $registrationIds[0] = $row["device_token"];      
        }
    }
    
    $conn->close();    

    $avatar_id = $_POST["avatar_id"];
    $controller_id = $_POST["controller_id"];
 }
else {
     $avatar_id = $_GET["avatar_id"];
    $controller_id = $_GET["controller_id"];
 }
?>
<!DOCTYPE html>
<html>
    
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>CONTROLLER</title>
        <link rel="stylesheet" href="main.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
       <script src="https://hammerjs.github.io/dist/hammer.js"></script>
        <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1">
        <script type="text/javascript">
            var activetab = <?php echo $active_tab ?>;
           var totalSeconds = <?php echo $totalSeconds ?>;
            var timer;
            
            function setTime() {
              ++totalSeconds;
              secondsLabel.innerHTML = pad(totalSeconds % 60);
              minutesLabel.innerHTML = pad(parseInt(totalSeconds / 60));
            }

            function pad(val) {
              var valString = val + "";
              if (valString.length < 2) {
                return "0" + valString;
              } else {
                return valString;
              }
            }
            function submitform(choice,whichform, duration)
            {
              
                var id = "command" + whichform;
                var id2 = "duration" + whichform;
                var id3 = "totalSeconds" + whichform;
                var formid = "commandsform" + whichform;
                
                document.getElementById(id).value = choice;
                document.getElementById(id2).value = duration;
                document.getElementById(id3).value = totalSeconds;
                document.getElementById(formid).submit();
  
            }
            function checkFeedback()
            {
             
                var error = <?php echo $error ?>;
                var sending = <?php echo $sentOK ?>;
            
                if(sending==0) {
                    document.getElementById("Feedback").style.display = "none";
                }
                else {
                    if (error==0) {
                        var latest_command = "<?php echo $command ?>";
                        if(latest_command === "begin"){
                            totalSeconds = 0;
                            latest_command = "begin_controller";
                        }
                        if(latest_command !== ""){
                           //set the timer...
                            timer = setInterval(setTime, 1000);
                         }
                        if(latest_command === "end"){
                           clearInterval(timer);
                            totalSeconds = totalSeconds + 9;
                            setTime();
                        }
                        //Check if audio is playing already
                        var  audioElement = document.getElementById('audioElement');
                        if(audioElement.currentTime > 0
                        && !audioElement.paused
                        && !audioElement.ended
                        && audioElement.readyState > 2){

                            //It's playing so don't do anything... 

                            }
                        else {
                            //Play command
                            audioElement.src = "./audio/" + latest_command + ".mp3";
                            audioElement.play();
                         }

                        var duration = <?php echo $duration ?> * 1000;
                        document.getElementById("Feedback").style.display = "block";
                        setTimeout(removeFeedback, duration);
       
                    }
                    else {
                        document.getElementById("errormessage").style.display = "block";
                    }
                }    
                document.getElementById("<?php echo $active_tab ?>").click();
                document.body.style.backgroundColor = "<?php echo $bgcolor ?>";
             
            }
            function removeFeedback()
             {
                 document.getElementById("Feedback").style.display = "none";
            }
            function openTab(evt, tabName, tabnr) {
                  var i, tabcontent, tablinks;
                  tabcontent = document.getElementsByClassName("tabcontent");
                  for (i = 0; i < tabcontent.length; i++) {
                    tabcontent[i].style.display = "none";
                  }
                  tablinks = document.getElementsByClassName("tablinks");
                  for (i = 0; i < tablinks.length; i++) {
                    tablinks[i].className = tablinks[i].className.replace(" active", "");
                  }
                  document.getElementById(tabName).style.display = "block";
                  evt.currentTarget.className += " active";
                  activetab = tabnr;
            }
            function resetTime()
            {
                clearInterval(timer);
                totalSeconds = 0;
                secondsLabel.innerHTML = "00";
                minutesLabel.innerHTML = "00";
             }
       
         </script>

    </head>
    <body onload="checkFeedback()">
         <audio id="audioElement"></audio>
        <div id="Feedback" class="overlay">
            <img src="images/sending.gif" width="100%"><br>
            Duration: <?php echo $duration ?> second(s)
        </div>
        <div id="errormessage" class="overlay">
            <img src="images/error.png"><br>
        Ooops! Something went wrong.<br>Please start over.<br><br>
            <a href="https://modgift.itu.dk/neverletmego/controller/start.php">Go back to start page!</a>
        </div>
       <div id="hitarea" class="hitarea">&nbsp&nbsp&nbsp</div>
        
        <div class="tab" >
            <button id="0" class="tablinks" onclick="openTab(event, 'Start', 0)">Start</button>
            <button id="1" class="tablinks" onclick="openTab(event, 'Basic', 1)">Basic</button>
            <button id="2" class="tablinks" onclick="openTab(event, 'Body', 2)">Emotions</button>
            <button id="3" class="tablinks" onclick="openTab(event, 'Questions', 3)">Questions</button>
            <button id="4" class="tablinks" onclick="openTab(event, 'Feelings', 4)">Feelings</button>
            <button id="5" class="tablinks" onclick="openTab(event, 'Becomings', 5)">Becomings</button>
            <button id="6" class="tablinks" onclick="openTab(event, 'Imaginings', 6)">Imaginings</button>
        </div>
        <div id="Start" class="tabcontent"> 
            <div id="title" class="title">NEVER LET ME GO</div>
            <div class="dots">
                <span class="dotactive"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>     
                <span class="dot"></span>     
            </div>
            
            <form id="commandsform0" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>"> 
                <input name="active_tab" type="hidden" value="0"> 
                <input id="command0" name="command" type="hidden"> 
                 <input id="duration0" name="duration" type="hidden"> 
                <input id="totalSeconds0" name="totalSeconds" type="hidden"> 
                <input id="bgcolor" name="bgcolor" type="hidden" value="#fabd03"> 
                <button id="begin" class="button" onClick="submitform('begin',0,32)">BEGIN</button><br>
                 <button id="end" class="button" onClick="submitform('end',0,10)">END</button><br><br>
                 <button id="are_you_ok" class="button" onClick="submitform('are_you_ok',0,10)">CHECK IF YOUR PARTNER IS DOING OK</button><br>
             </form>
                 <button id="reset_time" class="button" onClick="resetTime()">RESET TIME</button><br>
                 
           
                 <br>
                 Time since beginning:<br>
                    <label id="minutes">00</label>:<label id="seconds">00</label>
        </div>
        <div id="Basic" class="tabcontent"> 
            <div id="title" class="title">BASIC COMMANDS</div>
            <div class="dots">
                <span class="dot"></span>     
                <span class="dotactive"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>        
            </div>
            <form id="commandsform1" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>"> 
                <input name="active_tab" type="hidden" value="1"> 
                <input id="command1" name="command" type="hidden"> 
                <input id="duration1" name="duration" type="hidden"> 
                <input id="totalSeconds1" name="totalSeconds" type="hidden"> 
                 <input id="bgcolor" name="bgcolor" type="hidden" value="#fabd03"> 
                <button id="explore" class="button" onClick="submitform('explore',1, 1)">EXPLORE</button><br>
                <button id="wait" class="button" onClick="submitform('wait2',1, 1)">WAIT</button><br>
                <button id="go" class="button" onClick="submitform('go',1, 1)">GO</button><br>
                 <button id="follow" class="button" onClick="submitform('follow',1, 1)">FOLLOW</button><br>
                <button id="lead" class="button" onClick="submitform('take_lead',1, 2)">TAKE THE LEAD</button><br>
                 <button id="come_closer" class="button" onClick="submitform('come_closer',1, 2)">COME CLOSER</button><br>
                 <button id="turn_around" class="button" onClick="submitform('turn_around',1, 2)">TURN AROUND</button><br>
                <button id="look" class="button" onClick="submitform('look2',1,1)">LOOK</button><br>
                 <button id="look_up" class="button" onClick="submitform('look_up',1,1)">LOOK UP</button><br>
                 <button id="look_down" class="button" onClick="submitform('look_down',1,2)">LOOK DOWN</button><br>
                   <button id="look_at_me" class="button" onClick="submitform('look_at_me',1,2)">LOOK AT ME</button><br>
                    <button id="look_at_people" class="button" onClick="submitform('look_at_people',1,3)">LOOK AT PEOPLE</button><br>
                <button id="listen" class="button" onClick="submitform('listen',1,1)">LISTEN</button><br>
                <button id="touch" class="button" onClick="submitform('touch',1,1)">TOUCH</button><br>
                  <button id="do_what_you_want" class="button" onClick="submitform('do_what_you_want',1,2)">DO WHAT YOU WANT</button><br>
          
            </form>
        </div>
            <div id="Body" class="tabcontent"> 
            <div id="title" class="title">BODY</div>
           <div class="dots">
               <span class="dot"></span>     
                <span class="dot"></span>
                <span class="dotactive"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>        
            </div>
            <form id="commandsform2" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>">
                <input name="active_tab" type="hidden" value="2">
                <input id="command2" name="command" type="hidden"> 
                <input id="duration2" name="duration" type="hidden"> 
                <input id="totalSeconds2" name="totalSeconds" type="hidden"> 
                 <input id="bgcolor" name="bgcolor" type="hidden" value="#b4d4da"> 
                <button id="close_your_eyes" class="button" onClick="submitform('close_your_eyes',2,2)">CLOSE YOUR EYES</button><br>
                 <button id="open_your_eyes" class="button" onClick="submitform('open_your_eyes',2,2)">OPEN YOUR EYES</button><br>
                 <button id="breathe_deeply" class="button" onClick="submitform('breathe_deeply',2,2)">BREATHE DEEPLY</button><br>   
                <button id="hold_your_breath2" class="button" onClick="submitform('hold_your_breath2',2,2)">HOLD YOUR BREATH</button><br> 
                <button id="move_slower" class="button" onClick="submitform('move_slower',2,2)">MOVE SLOWER</button><br>
                <button id="move_faster" class="button" onClick="submitform('move_faster',2,2)">MOVE FASTER</button><br>
                <button id="move_to_the_rhythm" class="button" onClick="submitform('move_to_the_rhythm',2,2)">MOVE TO THE RHYTHM</button><br>
                 <button id="mimic" class="button" onClick="submitform('mimic',2,3)">MIMIC THIS WITH YOUR BODY</button><br>
                    <button id="shake" class="button" onClick="submitform('shake',2,1)">SHAKE</button><br>
                 <button id="sway" class="button" onClick="submitform('sway',2,1)">SWAY</button><br>
                <button id="reach_out" class="button" onClick="submitform('reach_out',2,1)">REACH OUT</button><br>
                <button id="odd_posture" class="button" onClick="submitform('odd_posture',2,3)">PUT YOURSELF IN AN ODD POSTURE</button><br>
                 <button id="let_go" class="button" onClick="submitform('let_go',2,1)">LET GO</button><br>
            </form>             
        </div>
        <div id="Questions" class="tabcontent"> 
            <div id="title" class="title">QUESTIONS</div>
            <div class="dots">
                <span class="dot"></span>     
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dotactive"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>        
            </div>
            <form id="commandsform3" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>">
                <input name="active_tab" type="hidden" value="3">
                <input id="command3" name="command" type="hidden"> 
                <input id="duration3" name="duration" type="hidden"> 
                <input id="totalSeconds3" name="totalSeconds" type="hidden"> 
                <input id="bgcolor" name="bgcolor" type="hidden" value="#ffffff"> 
                <button id="who_does_it_remind" class="button" onClick="submitform('who_does_it_remind',3,3)">WHO DOES IT REMIND YOU OF?</button><br>
                <button id="gift" class="button" onClick="submitform('gift',3,3)">WHO WOULD YOU GIVE THIS TO?</button><br>
                <button id="which_part_of_you" class="button" onClick="submitform('which_part_of_you',3,4)">WHAT PART OF YOU CAN YOU SEE IN THIS?</button><br>  
                <button id="memories" class="button" onClick="submitform('memories2',3,4)">DO YOU HAVE ANY MEMORIES RELATED TO THIS?</button><br>  
                <button id="part_of_your_life" class="button" onClick="submitform('part_of_your_life',3,3)">WHAT PART OF YOUR LIFE IS CONNECTED TO THIS?</button><br>  
                  <button id="dream" class="button" onClick="submitform('dream',3,2)">COULD THIS BE A DREAM OF YOURS?</button><br> 
                 <button id="where_are_you" class="button" onClick="submitform('where_are_you',3,2)">WHERE ARE YOU RIGHT NOW?</button><br> 
                <button id="long_for" class="button" onClick="submitform('long_for',3,3)">WHAT DO YOU LONG FOR RIGHT NOW?</button><br> 
                <button id="hold_back" class="button" onClick="submitform('hold_back',3,2)">WHAT MAKES YOU HOLD BACK?</button><br> 
                <button id="see_inside" class="button" onClick="submitform('see_inside',3,3)">WHAT DO YOU SEE INSIDE?</button><br>  
            </form>
        </div>
        <div id="Feelings" class="tabcontent"> 
            <div id="title" class="title">FEELINGS</div>
            <div class="dots">
                <span class="dot"></span>     
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dotactive"></span>
                <span class="dot"></span>
                <span class="dot"></span>        
            </div>
            <form id="commandsform4" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>">
                <input name="active_tab" type="hidden" value="4">
                <input id="command4" name="command" type="hidden"> 
                <input id="duration4" name="duration" type="hidden"> 
                <input id="totalSeconds4" name="totalSeconds" type="hidden"> 
                <input id="bgcolor" name="bgcolor" type="hidden" value="#aa88ac"> 
               
                <button id="spaciousness" class="button" onClick="submitform('spaciousness',4,3)">CAN YOU SENSE THE SPACIOUSNESS?</button><br>
               <button id="feel_longing" class="button" onClick="submitform('feel_longing',4,3)">CAN YOU FEEL THE LONGING IN THIS?</button><br>
                <button id="tenderness" class="button" onClick="submitform('tenderness',4,3)">CAN YOU FEEL THE TENDERNESS IN THIS?</button><br>
                 <button id="sense_passion" class="button" onClick="submitform('sense_passion2',4,4)">CAN YOU SENSE THE PASSION IN THIS?</button><br>
                 <button id="feel_sadness" class="button" onClick="submitform('feel_sadness',4,4)">CAN YOU FEEL THE SADNESS IN THIS?</button><br>
                  <button id="sense_anger" class="button" onClick="submitform('sense_anger',4,4)">CAN YOU SENSE THE ANGER IN THIS?</button><br>
                <button id="sense_pain" class="button" onClick="submitform('sense_pain',4,4)">CAN YOU SENSE THE PAIN IN THIS?</button><br>
                <button id="find_hope" class="button" onClick="submitform('find_hope',4,3)">CAN YOU FIND HOPE IN THIS?</button><br>
                 
                       
            </form>
        </div>
        <div id="Becomings" class="tabcontent"> 
            <div id="title" class="title">BECOMINGS</div>
            <div class="dots">
                <span class="dot"></span>     
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dotactive"></span>
                <span class="dot"></span>        
            </div>
            <form id="commandsform5" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>">
                <input name="active_tab" type="hidden" value="5">
                <input id="command5" name="command" type="hidden"> 
                <input id="duration5" name="duration" type="hidden"> 
                <input id="totalSeconds5" name="totalSeconds" type="hidden"> 
                 <input id="bgcolor" name="bgcolor" type="hidden" value="#cccccc"> 
                <button id="become_tense" class="button" onClick="submitform('become_tense',5,2)">BECOME TENSE</button><br>
                  <button id="become_heavy" class="button" onClick="submitform('become_heavy',5,1)">BECOME HEAVY</button><br>
                  <button id="become_light" class="button" onClick="submitform('become_light',5,1)">BECOME LIGHT</button><br>
                  <button id="become_sharp" class="button" onClick="submitform('become_sharp',5,1)">BECOME SHARP</button><br>
                  <button id="become_soft" class="button" onClick="submitform('become_soft',5,2)">BECOME SOFT</button><br>
                  <button id="become_tall" class="button" onClick="submitform('become_tall',5,1)">BECOME TALL</button><br>
                 <button id="become_small" class="button" onClick="submitform('become_small',5,1)">BECOME SMALL</button><br>
                 <button id="become_part_of_this" class="button" onClick="submitform('become_part_of_this',5,3)">BECOME A PART OF THIS</button><br>
               
            </form>
        </div>
        <div id="Imaginings" class="tabcontent"> 
            <div id="title" class="title">IMAGINE THAT...</div>
            <div class="dots">
                <span class="dot"></span>     
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dot"></span>
                <span class="dotactive"></span>        
            </div>
            <form id="commandsform6" action="index.php" method="post">
                <input name="avatar_id" type="hidden" value="<?php echo $avatar_id ?>"> 
                <input name="controller_id" type="hidden" value="<?php echo $controller_id ?>">
                <input name="active_tab" type="hidden" value="6">
                <input id="command6" name="command" type="hidden"> 
                <input id="duration6" name="duration" type="hidden"> 
                <input id="totalSeconds6" name="totalSeconds" type="hidden"> 
                 <input id="bgcolor" name="bgcolor" type="hidden" value="#6c8862"> 
                <button id="imagine_fall_apart" class="button" onClick="submitform('imagine_fall_apart',6,5)">EVERYTHING HERE IS ABOUT TO FALL APART</button><br>
                 <button id="imagine_floor" class="button" onClick="submitform('imagine_floor',6,3)">THE FLOOR IS SHAKING</button><br> 
                <button id="imagine_last_memories" class="button" onClick="submitform('imagine_last_memories',6,3)">THESE ARE YOUR LAST MEMORIES</button><br> 
                <button id="imagine_dust" class="button" onClick="submitform('imagine_dust',6,4)">YOU ARE BECOMING DUST</button><br> 
                <button id="imagine_beginning" class="button" onClick="submitform('imagine_beginning2',6,4)">THIS IS THE BEGINNING OF SOMETHING NEW</button><br>
                <button id="imagine_connected" class="button" onClick="submitform('imagine_connected',6,4)">EVERYTHING HERE IS CONNECTED</button><br>
                <button id="imagine_gift" class="button" onClick="submitform('imagine_gift',6,4)">THIS IS A GIFT FOR YOU</button><br> 
                <button id="imagine_looking_back" class="button" onClick="submitform('imagine_looking_back',6,5)">THIS IS LOOKING BACK AT YOU</button><br> 
            </form>
        </div>
   
        <script>
        var minutesLabel = document.getElementById("minutes");
        var secondsLabel = document.getElementById("seconds");
         var myElement = document.getElementById("hitarea");
            
        
        var hammertime = new Hammer(myElement); 
   
        var left = true;
        hammertime.on('panleft', function(ev) {
            left = true;
             });
        hammertime.on('panright', function(ev) {
            left = false;
             });
        hammertime.on('panend', function(ev) {
            if(left)  {  
                switch(activetab) {
                        case 0:
                            document.getElementById("1").click();
                            document.body.style.backgroundColor = "#fabd03";
                        break;
                        case 1:
                            document.getElementById("2").click();
                            document.body.style.backgroundColor = "#b4d4da";
                        break;
                        case 2:
                            document.getElementById("3").click();
                            document.body.style.backgroundColor = "white";
                        break;
                        case 3:
                            document.getElementById("4").click();
                            document.body.style.backgroundColor = "#aa88ac";
                        break;
                          case 4:
                            document.getElementById("5").click();
                            document.body.style.backgroundColor = "#cccccc";
                        break;
                        case 5:
                            document.getElementById("6").click();
                            document.body.style.backgroundColor = "#6c8862";
                        break;
                        case 6:
                            //do nothing for now
                        break;
                 }
           }
            else {

                switch(activetab) {
                        case 0:
                            //do nothing
                        break;
                        case 1:
                            document.getElementById("0").click();
                            document.body.style.backgroundColor = "#fabd03";
                        break;
                        case 2:
                            document.getElementById("1").click();
                            document.body.style.backgroundColor = "#fabd03";
                        break;
                        case 3:
                            document.getElementById("2").click();
                            document.body.style.backgroundColor = "#b4d4da";
                        break;
                        case 4:
                            document.getElementById("3").click();
                            document.body.style.backgroundColor = "white";
                        break;
                         case 5:
                            document.getElementById("4").click();
                            document.body.style.backgroundColor = "#aa88ac";
                        break;
                         case 6:
                            document.getElementById("5").click();
                            document.body.style.backgroundColor = "#cccccc";
                        break;
                 }
             }
        });
            

            
        </script>
    </body>
</html>

