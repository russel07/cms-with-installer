<?php

require_once __DIR__.'/include/HomeClass.php';
$env = __DIR__.'/.env';
$home = new HomeClass($env);

if(!$home->isInstalled){
    header("Location:installer.php");
}

$loggedIn = $home->isLoggedIn();
$username = $home->getLoggedInUserName();

if(!$loggedIn){
    header("Location:login.php");
}

$home->logout();

header("Location:index.php");
?>

