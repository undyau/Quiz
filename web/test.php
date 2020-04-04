<?php
session_start();
require_once(__DIR__.'/ini.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<?php
echo("<title>".TITLE."</title>");
?>
</head>
<script>
window.onload = function() {
    var loginForm = document.getElementById("LoginForm");
    loginForm.addEventListener("submit", function() {
         login(loginForm);
     });
 };
 
function nextQuestion() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.responseType = "json";
<?php
    global $BaseUrl;
    echo ("    xmlhttp.open('post', '".$BaseUrl."/nextquestion.php', true);\n");
?>
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            nextQuestionResults(xmlhttp);
        }
    }
    var params = 'user=' + document.getElementById('UserDiv').textContent;
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);
}
 
 function nextQuestionResults(xmlhttp) {
    console.log('response', xmlhttp.response);
    console.log(typeof xmlhttp.response);
    var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
    if (myArr['status'] == "OK") {
      const myNode = document.getElementById("UserDiv");
      myNode.textContent = myArr['User'] + "    Score: " + myArr['score'];
      
      questionDiv = document.getElementById("QuestionDiv");
      if (questionDiv == null)
      {
        var questionDiv = document.createElement("div");
        questionDiv.id = "QuestionDiv";
        var questionForm = document.createElement("form");
        questionForm.id = "QuestionForm";
        questionDiv.appendChild(questionForm);
        var questionCombo = document.createElement("select");
        questionCombo.id = "QuestionCombo";
        questionForm.appendChild(questionCombo);
        var answerCombo = document.createElement("select");
        answerCombo.id = "AnswerCombo";
        questionForm.appendChild(answerCombo);
        
        document.body.insertBefore(questionDiv, myNode.nextSibling)
      }
    }
}
 
function login() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.responseType = "json";
<?php
    global $BaseUrl;
    echo ("    xmlhttp.open('post', '".$BaseUrl."/login.php', true);\n");
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
    console.log('response', xmlhttp.response);
    console.log(typeof xmlhttp.response);
    var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
    var badLogin = document.getElementById("BadLogin");
    var un = document.getElementById("Username");

    if  (myArr['LogonStatus'] != 'success') {
        badLogin.style.display = "block";
        loggedIn.style.display = "none";
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
    newContent.id="UserLabel";
    
    nextQuestion();
    }
}
</script>
<body>
<?php
echo("<H1>".TITLE."</H1>");
?>
<div id="LoginDiv">
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
        <input type="submit" value="Login">
    </div>
    <div id="BadLogin" style="display:none">
        <p>The login information you entered does not match
        a user/password combination in our records. Please try again.</p>
    </div>
</form>
</div>

</body>
</html>