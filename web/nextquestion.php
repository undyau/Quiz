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
    if (!array_key_exists("questions", $response))
      $response["finished"]="1";
    else
      $response["finished"]="0";
    
    $query = "SELECT score from user where name = '$user'";
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result)
    {
      $row = mysqli_fetch_array ($result, MYSQLI_ASSOC);
      $response["score"] = $row["score"];
    }
    else
      $response["score"][] = "error fetching score";
    
    $query = 'SELECT name, score from user where score > 0 order by score desc';
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result)
    {
      while ($row = mysqli_fetch_array ($result, MYSQLI_ASSOC))
        $response["leaders"][] = $row['name']." - ".$row['score']; 
      if (!array_key_exists('leaders', $response))
        $response["leaders"][] = "no-one has scored yet";
    }
    else
      $response["status"] = "SQL failure getting leaders";
  }
  else
    $response["status"] = "SQL failure";
}
echo json_encode($response);
?>