<?php 

function emptyInputSignup($username, $email, $password, $repeatpassword){
    $result;
    if(empty($username) || empty($email) || empty($password) || empty($repeatpassword)){
        $result = true;
    }
    else{
        $result = false;
    }
    return $result;
}

function InvalidUsername($username){
    return !preg_match("/^[a-zA-Z]+$/", $username);
}

function InvalidEmail($email){
    $result;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $result = true;
    }
    else{$result=false;}
}

function pwdMatch($password, $repeatpassword){
    $result;
    if($password !== $repeatpassword){
        $result = true;
    }
    else{$result=false;}
}

function usernameExists($db, $username, $email){
    $result;
    $sql = "SELECT * FROM users WHERE userID = ? OR userEmail = ?;";
    $stmt = mysqli_stmt_init($db);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: /scheduling_app/signup.php?error=statementfailed");
        exit();
    }

    mysqli_stmt_bind_param($stmt, "ss", $username, $email);
    mysqli_stmt_execute($stmt);

    $resultData = mysqli_stmt_get_result($stmt);

    if($row = mysqli_fetch_assoc($resultData)){
        return $row;
    }
    else{
        $result = false;
        return $result;
    }

    mysqli_stmt_close($stmt);
}

function createUser($db, $username, $email, $password){
    $result;
    $sql = "INSERT INTO users(username, userEmail, userPassword) values (?, ?, ?);";
    $stmt = mysqli_stmt_init($db);
    if(!mysqli_stmt_prepare($stmt, $sql)){
        header("location: /scheduling_app/signup.php?error=statementfailed");
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hasehdPassword);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("location: /scheduling_app/index.php?error=none");
    exit();
}
?>