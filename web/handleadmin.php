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
  $action = $_POST['action'];
  if ($action == "reset")
  {
    $query = "delete from response where 1";
    $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
    if ($result)
    {
      $query = "update user set score = 0 where 1";
      $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
      if ($result)
        $response["status"] = "OK";
      else
        $response["status"] = "SQL failure clearing scores (table user)";
    }
    else
      $response["status"] = "SQL failure clearing response table";
  }
  else
  {
    $response["status"] = "invalid action";
  }
}
echo json_encode($response);
?>