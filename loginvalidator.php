<?php
session_start();

if (isset($_POST["submit"])) {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $valid_username = "derek";
    $valid_password = "derek";

    if ($username === $valid_username && $password === $valid_password) {
        
        $_SESSION["username"] = $username;
        $_SESSION["loggedin"] = true;
        header("Location: /scheduling_app/index.php?"); 
        exit();
    } else {
        
        header("Location: /scheduling_app/index.php?invalidlogin");

        exit();
    }
}
?>