<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<script>
window.onload = function() {
    var loginForm = document.getElementById("LoginForm");
    loginForm.addEventListener("submit", function() {
         login(loginForm);
     });
 };
 
function login(form) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.open("post", "https://quiz.bigfootorienteers.com/login.php", true);
    xmlhttp.onreadystatechange = function () {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            loginResults(form, xmlhttp);
        }
    }
		xmlhttp.send();
}
 
 function loginResults(form, xmlhttp) {
    var loggedIn = document.getElementById("LoggedIn");
    var badLogin = document.getElementById("BadLogin");
		var un = document.getElementById("Username");

    if (xmlhttp.responseText.indexOf("failed") == -1) {
        loggedIn.innerHTML = "Logged in as " + xmlhttp.responseText;
        loggedIn.style.display = "block";
        badLogin.style.display = "none";
    } else {
        badLogin.style.display = "block";
				loggedIn.style.display = "none";
        un.select();
        un.className = "Highlighted";
        setTimeout(function() {
            badLogin.style.display = 'none';
        }, 3000);
    }
}
</script>
<body>
<form id="LoginForm" onsubmit="return false">
    <h1>Login Form</h1>
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
    <div id="BadLogin">
        <p>The login information you entered does not match
        an account in our records. Please try again.</p>
    </div>
		<div id="LoggedIn">
        <p>Sayin nothing.</p>
    </div>
</form>
</body>
</html>