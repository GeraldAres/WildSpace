<?php
session_start();

session_unset();
session_destroy();

header("Location: ../../test_screens/login.php");
exit();
?>