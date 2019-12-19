'use strict';

var device_id = "";
var incommingcommand = "";
var command_id = "";
var command_timestamp = "";
var no_connection = 0;
var no_connection_warning = 0;


function registeruser()
 {
     //if new regiters
     var url = "https://yourserver/device_registration.php?device_token=" + device_id;
    if (no_connection==0) 
        fetch(url);
}
function makeid(length) {
   var result           = '';
   var characters       = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
   var charactersLength = characters.length;
   for ( var i = 0; i < length; i++ ) {
      result += characters.charAt(Math.floor(Math.random() * charactersLength));
   }
   return result;
}


function showCode() {

    var codeblock = document.getElementById("code");

  if (device_id!=="") {
    //  console.log("To start give this code to your friend: " + device_id);
      codeblock.textContent = "To start give this code to your friend: " + device_id; //device_id.substring(0, 7);
      codeblock.style.display = "block"; 
      document.getElementById("showlink").style.display = "none"; 
      codeblock.addEventListener('click', function(){
        document.getElementById("code").style.display = "none";
        document.getElementById("showlink").style.display = "block";                         
      });

  } 
}

function play_sound(){
 
    var command = incommingcommand;
    if (command!==""){
        console.log("PLAY: " + command);
        var soundsrc = "./audio/" + command + ".mp3";
        
         var  audioElement = document.getElementById('audioElement');
   
        //Check if audio is playing already
        if(audioElement.currentTime > 0
        && !audioElement.paused
        && !audioElement.ended
        && audioElement.readyState > 2){
            
            //It's playing so don't do anything... 
            
            }
        else {
         //var  audioElement = new Audio();
            audioElement.src = "./audio/" + command + ".mp3";
            var playPromise = audioElement.play();
            
            if (playPromise !== undefined) {
              playPromise.then(function() {
                // Automatic playback started!
                  document.getElementById("cmd_play").disabled = true;
                  document.getElementById("cmd_play").style.display = "none";
              }).catch(function(error) {
                // Automatic playback failed.
                // Show a UI element to let the user manually start playback.
                document.getElementById("cmd_play").disabled = false;
                document.getElementById("cmd_play").style.display = "block";
                document.getElementById("cmd_play").addEventListener('click', play_sound);
              });
            }
        }
    }
} 

function doesConnectionExist() {
    if(navigator.onLine)
    {
        no_connection = 0;
        no_connection_warning=0;
    }
    else
    {
        no_connection = 1;
    }
}
    

function getCommand() {
    //Checking the Internet connection
    doesConnectionExist();
    
   if (no_connection==1) {
        if (no_connection_warning==0) {
            //window.alert("You have lost your Internet connection!");
            no_connection_warning=1;
        }
    }
    else {
        
        if(device_id!==""){
            var url = 'https://yourserver/avatar/getcommand.php?device_token=' + device_id;

        //call getData function
        //getData(url)
        fetch(url)
          .then(function(response) {
            if (!response.ok) {
                throw Error(response.statusText);
            }
            return response.json();
          })
        .then(function(data) {
            if (data !== null) {
                var command = data["command"];
                //Check if command is empty...
                if (command!==""){
                    console.log("Command: " + command);
                    var timestamp = data["created_at"];
                    console.log("Timestamp: " + timestamp);
                    var comid = data["command_id"];

                    //Check if the command is new
                    if (command_id !== comid) {
                        incommingcommand = command;
                        command_timestamp = timestamp;
                        command_id = comid;
                        play_sound();
                        //Set played to TRUE
                        url = "https://yourserver/avatar/setplayed.php?comid=" + comid;
                        fetch(url);
                        no_connection = 0;
                    }
                }
                else
                    console.log("Command is empty! ");
             }
        }).catch(function(error) {
            console.log(error);
        });

    }
  }
}

function getCookie(name) {
    console.log("Cookie: " + document.cookie);
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function setCookie(name,value,days) {
    var expires = "";
    if (days) {
        var date = new Date();
        date.setTime(date.getTime() + (days*24*60*60*1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + (value || "")  + expires + "; path=/";
}
function eraseCookie(name) {   
    document.cookie = name+'=; Max-Age=-99999999;';  
}

//Check for cookie for avatarid
var name = "Avatarid";
var avatarid = "";
var newuser = false;
//eraseCookie(name);
avatarid = getCookie(name);
if (avatarid!== null){ 
    console.log("Avatarid from cookie: " + avatarid);
    device_id = avatarid; 
    newuser = false;
}
else
{   
    //make a new id and register it
    device_id  = makeid(10);
    console.log("New device id: " + device_id);
    setCookie(name,device_id,100);
    registeruser();
    newuser = true;
}

 window.onload = function(){
     if (newuser==false)
        document.getElementById("code").style.display = "none";
     else
         showCode(); 
     
        document.getElementById("showcode").onclick = function(){
                   showCode(); 
        }
    }


