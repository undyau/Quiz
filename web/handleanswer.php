<?php
session_start();
require_once(__DIR__.'/mysqli_connect.php');
require_once(__DIR__.'/ini.php');
require_once(__DIR__.'/trace.php');
?>

<?php
$response = array();
// Check Password
$user = $_POST['user'];
$response["User"] = $user;

if ($_SESSION["LoggedOn"]!=1)
{
  $response["status"] = "not logged on";
}
else
{
  // prepare thing ?
  $question = mysqli_real_escape_string($mysqli, $_POST['question']);
  $answer = mysqli_real_escape_string($mysqli, $_POST['answer']);

  // Make sure inputs are valid
  if (!ctype_digit($question))
    $response["status"] = "invaid question";
  elseif (!ctype_alpha($answer))
    $response["status"] = "invalid answer";
  else
  {
    // Do the insert using prepared statement
    $stmt = $mysqli->prepare("INSERT into response (user, number, answer) VALUES(?,?,?)");
    if ($stmt === false) 
    {
      trigger_error($mysqli->error, E_USER_ERROR);
      $response["status"] = "SQL error on prepare";
    }
    else
    {
      $stmt->bind_param('sis', $user,  $question,  $answer);
      $status = $stmt->execute();
      if ($status === false) 
        $response["status"] = "SQL error on execute";
      else
      {
        $query = "update user set score = (select count(*) from question, response where response.user=\"$user\" and response.number = question.number and question.answer = response.answer) where name = \"$user\"";
        $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
        if ($result)
          $response["status"] = "OK";
        else
          $response["status"] = "SQL failure";
      }
    }
  }
}
$response["user"] = $user;
echo json_encode($response);
?>