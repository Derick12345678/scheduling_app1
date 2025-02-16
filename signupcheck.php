<?php
    if(isset($_POST["submit"])){
        $username = $_POST["username"];
        $email = $_POST["email"];
        $password = $_POST["password"];
        $repeatpassword = $_POST["repeatpassword"];

        include('config.php');
        include('signupfunctions.php');

        if(emptyInputSignup($username, $email, $password, $repeatpassword) !== false){
            header("location: /scheduling_app/signup.php?error=emptyinput");
            exit();
        }
        if(InvalidUsername($username) !== false){
            header("location: /scheduling_app/signup.php?error=invalidusername");
            exit();
        }
        if(InvalidEmail($email) !== false){
            header("location: /scheduling_app/signup.php?error=invalidemail");
            exit();
        }
        if(pwdMatch($password, $repeatpassword) !== false){
            header("location: /scheduling_app/signup.php?error=passwordsdontmatch");
            exit();
        }
        if(usernameExists($db, $username, $email) !== false){
            header("location: /scheduling_app/signup.php?error=usernametaken");
            exit();
        }
        if(createUser($db, $username, $email, $password) !== false){
            header("location: /scheduling_app/signup.php?error=emptyinput");
            exit();
        }
        createUser($db, $username, $email, $password);
        header("location: /scheduling_app/login.php?pleaselogin");
        exit();
    }
    else{
        header("location: /scheduling_app/poop.php?");
        exit();
    }

?>