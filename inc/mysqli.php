<?php
$db = mysqli_connect($hostnameBDD, $userBDD, $passBDD, $database);
if (mysqli_connect_errno()) {echo "Failed to connect to MySQL: ".mysqli_connect_error();}
?>
