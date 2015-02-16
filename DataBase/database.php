<?php
	$mysqli = new mysqli(HOST, USER, PASS, BASE);
	if (mysqli_connect_errno()) trigger_error(mysqli_connect_error());
?>