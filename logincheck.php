<?php 
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("Location: scheduling_app/login.php"); 
    exit();
}
?>