<?php 
// redirect to the login page which is the landing page
session_start();
session_unset();
session_destroy();
header("Location: index.php");
exit(); 

?>