<?php
if(isset($_POST['user'])) {
    if($_POST['user']==EMAIL_ADDRESS && $_POST['password']==PASSWORD) {
        $_SESSION['user'] = EMAIL_ADDRESS;
    } else {
        $_GET['logout'] = true;
    }
}

if(isset($_GET['logout'])) {
    unset($_SESSION['user']);
    $_SESSION = array();
    session_unset();
    session_destroy();
    header('Location:  ./');
}

if(!isset($_SESSION['user']) || is_null($_SESSION['user']) || $_SESSION['user']=='') {
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebChangesTracker</title>
    <link rel="stylesheet" href="./style.css">
</head>
<body>
<h1>WebChangesTracker</h1>
<form action="./" method="POST">
    <p>
        <label for="user">E-mail</label>
        <input type="text" name="user" id="user">
    </p>
    <p>
        <label for="password">Password</label>
        <input type="password" name="password" id="password">
    </p>
    <p>
        <input type="submit" value="Login" />
    </p>
</form>
</body>
</html><?php
    exit;
}