<?php
require_once(__DIR__.'/mysqli_connect.php');
require_once(__DIR__.'/ini.php');
require_once(__DIR__.'/trace.php');
$user = "";
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<?php
echo("<title>".TITLE."</title>");
?>
<link rel="stylesheet" href="http://www.w3.org/StyleSheets/Core/Chocolate" type="text/css">
<script language="JavaScript">
function sendChange() {
    return true;
}
</script>
</head>
<body>

<?php
function do_insert()
{
    global $mysqli;
		global $user;
    
    // Check Password
    if (!array_key_exists('which', $_POST) && !isset($_POST['hash']) )
    {
        echo '<p style="color:red">missing password</p>';
        return;
    }
    $query = "SELECT name, password, score, adjustment from user where name = '".$_POST['competitor']."'";
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result !== false) 
    {
			$row = $result->fetch_assoc();
			if (!array_key_exists('which', $_POST))   // Not great - persist password value across posts ?
			{
			$hash = $row['password'];
			$myhash=hash("sha256",$_POST['competitor'].$_POST['hash']);
			if ($hash != $myhash)
					{
							echo "<p style='color:red'>invalid user or password</p>";
							return;
					}
			}
					
			echo "<p>Current score for ".$row['name'].": ".($row['score'] + $row['adjustment'])."</p>";
			setcookie("username", $_POST['competitor'], [expiry_time], [cookie_path], [domain], [secure], [httponly]);
    }
		else
		{
			echo "<p style='color:red'>invalid user</p>";
			echo "<p>".$_POST['competitor']."</p>";
			return;
		}

		$user = mysqli_real_escape_string($mysqli, $_POST['competitor']);
		if (array_key_exists('which', $_POST))
		{
			$question = mysqli_real_escape_string($mysqli, $_POST['which']);
			$answer = mysqli_real_escape_string($mysqli, $_POST['answer']);
    
			// Make sure inputs are valid
			if (!ctype_digit($question))
					return;

			if (!ctype_alpha($answer))
					return;

			// Do the insert using prepared statement
			$stmt = $mysqli->prepare("INSERT into response (user, number, answer) VALUES(?,?,?)");
			if ($stmt === false) 
			{
					trigger_error($mysqli->error, E_USER_ERROR);
					return;
			}

			// bind our parameters to avoid SQL injection
			$stmt->bind_param('sis', $user,  $question,  $answer);

			// Execute the prepared insert
			$status = $stmt->execute();
			if ($status === false) 
			{
					trigger_error($stmt->error, E_USER_ERROR);
					return;
			}
			else
			{
					echo "<p style='color:green'>Entered $answer for question $question</p>";
					echo "\r\n";
			} 
		}
}


    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
    {
        do_insert();
    }

    echo'<form onsubmit="return sendChange()" method=post>';
    echo "\r\n";
		echo '<label for="competitor">Contestant</label>';
    echo '<input size="30" name="competitor">';
    echo '<label for="hash">Password</label>';
    echo '<input type="password" size="16" name="hash"><br/>';
    echo "\r\n";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') 
		{		
			$query = "SELECT number from question where number not in (select number from response where user = '$user')";
			$result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);

			if ($result)
			{
				echo "<select name='which'>";
				while ($row = mysqli_fetch_array ($result, MYSQLI_ASSOC))
						{
						// Add the unanswered questions to a list for a drop down
						echo "<option value='" . $row['number'] . "'>" . $row['number'] . "</option>";
						}
				echo "</select>";
				echo "<select name='answer'>\r\n";
				$choices = explode(",",CHOICES);
				foreach ($choices as $choice)
					echo("<option value='".$choice."'>".$choice."</option>\r\n");  
				echo "</select>\r\n";	
			}
    }
		echo '<input type="submit" value="Submit"/>';
    echo '</form>';
?>


</body>
</html>