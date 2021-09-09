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
$pageId = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page']: '';

if($home->deletePage($pageId)){
    header("Location:admin.php");
}else{
    $title = "Content not found";
}
header("Location:admin.php");
?>

