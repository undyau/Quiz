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
 
function handleAnswer(answer) {
  var xmlhttp = new XMLHttpRequest();
  xmlhttp.responseType = "json";
<?php
    global $BaseUrl;
    echo ("  xmlhttp.open('post', '".$BaseUrl."/handleanswer.php', true);\n");
?>

  xmlhttp.onreadystatechange = function () {
      if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
          handleAnswerResults(xmlhttp);
      }
  }

  var select = document.getElementById('QuestionCombo');
  var params = 'user=' + document.getElementById('UserDiv').textContent + '&question=' + select.options[select.selectedIndex].text + '&answer=' + answer;
  xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
  xmlhttp.send(params);
}
 
function handleAnswerResults(xmlhttp)
{
  console.log('handleAnswerResults response', xmlhttp.response);
  var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
  if (myArr['status'] == "OK") 
    nextQuestion();
}
 
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
    console.log('nextQuestionResults response', xmlhttp.response);
    var myArr = JSON.parse(JSON.stringify(xmlhttp.response));
    if (myArr['status'] == "OK") {
      const myNode = document.getElementById("UserDiv");
      myNode.textContent = myArr['User'];
      
      questionDiv = document.getElementById("QuestionDiv");
      
      // Create the question/answer form if it doesn't exist
      if (questionDiv == null)
      {
        var questionDiv = document.createElement("div");
        questionDiv.id = "QuestionDiv";
        questionDiv.classList.add('form-style-3');
        
        var scoreDiv = document.createElement("div");
        scoreDiv.id = "ScoreDiv";
//        scoreDiv.textContent = "Score: " + myArr['score'];
        questionDiv.appendChild(scoreDiv);
      
        var questionForm = document.createElement("form");
        questionForm.id = "QuestionForm";
        questionDiv.appendChild(questionForm);

        var questionLabel = document.createElement("label");
        questionLabel.htmlFor = "QuestionCombo";
        questionLabel.innerHTML="Question:";
        questionForm.appendChild(questionLabel);

        var questionCombo = document.createElement("select");
        questionCombo.id = "QuestionCombo";
        questionForm.appendChild(questionCombo);
        
      // Create buttons for the possible answers
<?php
        $choices = explode(",",CHOICES);
        foreach ($choices as $choice) {
          echo("        var option".$choice." = document.createElement('button');\n");
          echo("        option".$choice.".value = '".$choice."';\n");
          echo("        option".$choice.".addEventListener('click',function(event) {handleAnswer('".$choice."'); event.preventDefault();});\n");
          echo("        var newContent".$choice." = document.createTextNode('".$choice."');\n"); 
          echo("        option".$choice.".appendChild(newContent".$choice.");\n"); 
          echo("        questionForm.appendChild(option".$choice.");\n");
        }
?>        
        document.body.insertBefore(questionDiv, myNode.nextSibling)
      }
      
      // Refresh the question list
      questionCombo = document.getElementById("QuestionCombo");
      while (questionCombo.firstChild) 
        questionCombo.removeChild(questionCombo.lastChild);
      if (myArr['finished'] == '0')
      {
        for (var i = 0; i < myArr['questions'].length; i++) {
          var option = document.createElement("option");
          option.value = myArr['questions'][i];
          option.text = myArr['questions'][i];
          questionCombo.appendChild(option);
        }
      }
      else
      {
        var resultDiv = document.createElement("div");
        resultDiv.id = "ResultDiv";
        resultDiv.innerHTML = "You have finished";
        questionDiv.appendChild(resultDiv);
        questionDiv = document.getElementById("questionForm");
        if (questionForm)
          questionForm.textContent = ""; 
      }
      
      scoreDiv = document.getElementById("ScoreDiv");
      scoreDiv.textContent = "Score: " + myArr['score'];
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
    console.log('loginResults response', xmlhttp.response);
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