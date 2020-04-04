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
  $query = "SELECT number from question where number not in (select number from response where user = '$user')";
  $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
  if ($result)
  {
    while ($row = mysqli_fetch_array ($result, MYSQLI_ASSOC))
      $response["questions"][] = $row['number']; 
    $response["status"] = "OK";
    $query = "SELECT score from user where name = '$user'";
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result)
    {
      $row = mysqli_fetch_array ($result, MYSQLI_ASSOC);
      $response["score"] = $row["score"];
    }
    else
      $response["score"] = "unavailable";
    
    $query = 'SELECT name, score from user where score in (SELECT MAX(score) from user) and score > 0';
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result && $result->num_rows == 1)
    {
      $row = mysqli_fetch_array ($result, MYSQLI_ASSOC);
      $response["leaders"] = $row["name"]." leading on ".$row["score"]." points";
    }
    elseif ($result && $result->num_rows > 1)
    {
      $row = mysqli_fetch_array ($result, MYSQLI_ASSOC);
      $response["leaders"] = $result->num_rows." on ".$row["score"]." points";
    }
    else
      $response["leaders"] = "unavailable";
  }
  else
    $response["status"] = "SQL failure";
}
echo json_encode($response);
?>