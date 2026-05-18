<?php
session_start();

session_unset();
session_destroy();

header("Location: ../../client_side/login.php");
exit();
?>