<?php
session_start();
require_once(__DIR__.'/mysqli_connect.php');
require_once(__DIR__.'/ini.php');
require_once(__DIR__.'/trace.php');
?>


<?php
$response = array();
// Check Password
$_SESSION["LoggedOn"]=0;
if (!array_key_exists('password', $_POST))
{
  $response["LogonStatus"] = "failed";
  $response["ErrorDetail"] = "Failed initial sanity checks pw";
}
elseif (!array_key_exists('user', $_POST) )
{
  $response["LogonStatus"] = "failed";
  $response["ErrorDetail"] = "Failed initial sanity checks user";
}
else
{
  $query = "SELECT password from admin where name = '".$_POST['user']."'";
  $result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
  if ($result !== false) 
  {
    $row = $result->fetch_assoc();
    $hash = $row['password'];
    $myhash=hash("sha256",$_POST['user'].$_POST['password']);
    if ($hash != $myhash)
    {
      $response["LogonStatus"] = "failed";
      $response["ErrorDetail"] = "Wrong user or password";
    }
    else
    {
      $response["LogonStatus"] = "success";
      $_SESSION["LoggedOn"]=1;
    }
  }
  else
  {
    $response["LogonStatus"] = "failed";
    $response["ErrorDetail"] = "Error executing SQL";
  }
}
$response["User"] = $_POST['user'];
echo json_encode($response);
?>