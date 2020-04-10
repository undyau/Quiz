<?php
session_start();
require_once(__DIR__.'/ini.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<?php
echo("<title>".TITLE." - Administration</title>");
?>
<link rel="stylesheet" href="form.css?<?php echo date('l jS \of F Y h:i:s A'); ?>" media="all" />
</head>
<script>
function doLogin(e) {
  var loginForm = document.getElementById("LoginForm");
  login(loginForm);
}

window.onload = function() {
    var loginButton = document.getElementById("LoginButton");
    loginButton.addEventListener("click", doLogin);
 };
 
function handleAction(action) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.responseType = "json";
<?php
    global $BaseUrl;
    echo ("  xmlhttp.open('post', '".$BaseUrl."/handleadmin.php', true);\n");
?>

  xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          handleActionResults(xmlhttp);
      }
  }

  var params = 'user=' + document.getElementById('UserDiv').textContent + '&action=' + action;
  xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xmlhttp.send(params);
}
 
function handleActionResults(xmlhttp)
{
  console.log('handleActionResults response', xmlhttp.response);
  var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
  alert(myArr['status']);
}
 
function login() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.responseType = "json";
<?php
    global $BaseUrl;
    echo ("    xmlhttp.open('post', '".$BaseUrl."/adminlogin.php', true);\n");
?>
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            loginResults(xmlhttp);
        }
    }
    var params = 'user=' + document.getElementById('Username').value + '&password=' + document.getElementById('Password').value;
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}
 
function loginResults(xmlhttp) {
    console.log('loginResults response', xmlhttp.response);
    var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
    var badLogin = document.getElementById("BadLogin");
    var un = document.getElementById("Username");

    if  (myArr['LogonStatus'] != 'success') {
        badLogin.style.display = "block";
//        loggedIn.style.display = "none";
        un.select();
        un.className = "Highlighted";
        setTimeout(function() {
            badLogin.style.display = 'none';
        }, 3000);
    }
    else
    {
      const myNode = document.getElementById("LoginDiv");
      myNode.textContent = '';

      var newDiv = document.createElement("div"); 
      var newContent = document.createTextNode(myArr['User']); 
      newDiv.appendChild(newContent);  
      document.body.insertBefore(newDiv, myNode); 
      newDiv.id="UserDiv";

      var adminForm = document.createElement("form");
      document.body.insertBefore(adminForm, myNode.nextSibling)
      adminForm.id = "AdminForm";
      
      var optionReset = document.createElement('button');
      optionReset.value = "reset";
      optionReset.addEventListener('click',function(event) {handleAction('reset'); event.preventDefault();});
      var newContentReset = document.createTextNode('Reset Scores');
      optionReset.appendChild(newContentReset);
      adminForm.appendChild(optionReset);
    }
}
</script>
<body>
<?php
echo("<H1>Quiz Administration</H1>");
?>
<div id="LoginDiv">
<div class="form-style-3">
<form id="LoginForm" onsubmit="return false">
    <div class="FormRow">
        <label for="Username">Username:</label>
        <input type="text" size="15" id="Username" name="Username">
    </div>
    <div class="FormRow">
        <label for="Password">Password:</label>
        <input type="password" size="15" id="Password" name="Password">
    </div>
    <div class="FormRow" id="LoginButtonDiv">
        <input type="button" value="Login" onclick="doLogin" id="LoginButton">
    </div>
    <div id="BadLogin" style="display:none">
        <p>The login information you entered does not match
        a user/password combination in our records. Please try again.</p>
    </div>
</form>
</div>
</div>

</body>
</html>