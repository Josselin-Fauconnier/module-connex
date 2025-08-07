<?php
session_start();
if(session_status()===PHP_SESSION_ACTIVE){
    $_SESSION=array();
    session_destroy();
}

header("location: index.php");
exit();
?>

