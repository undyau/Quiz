<?php
session_start();
require_once(__DIR__.'/mysqli_connect.php');
require_once(__DIR__.'/ini.php');
require_once(__DIR__.'/trace.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="content-type">
<?php
echo("<title>".TITLE."</title>");
?>
</head>
<body>
<?php
echo("<H1>".TITLE."</H1>");
?>

<table>
<tr><th style="text-align:left">Position</th><th style="text-align:left">Name</th><th style="text-align:left">Score</th></tr>
<?php
$query = "SELECT name, score from user where score > 0 order by score desc";
$result = $mysqli->query ($query) or trigger_error($mysqli->error." ".$query);
if ($result !== false) 
{
  $tie = false;
  $pos = 0;
  $lastScore = 0;
  $count = 0;
  while ($row = $result->fetch_assoc())
  {
    $count++;
    if ($row['score'] != $lastScore)
    {
      $lastScore = $row['score'];
      $pos = $count;
      $tie = false;
    }
    else
      $tie = true;
    echo "<tr><td style='text-align:left'>".$pos.($tie ? "=" : ".")."</td><td style='text-align:left'>".$row['name']."</td><td  style='text-align:right'>".$lastScore."</td></tr>";
  }
}
?>
</table>

</body>
</html>
