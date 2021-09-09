<?php
require_once __DIR__.'/include/Basic.php';
require_once __DIR__.'/include/HomeClass.php';
$env = __DIR__.'/.env';
$basic = new Basic($env);

if(!$basic->isInstalled){
    header("Location:installer.php");
}

$pageId = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id']: '';

if(!$pageId){
    header("Location:404.php");
}

$home = new HomeClass($env);
$pages = $home->getPages();
$page = $home->getPagesById($pageId);
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <title>Installer</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="stylesheet" type="text/css" href="asset/css/bootstrap.min.css"/>
    <link rel="stylesheet" type="text/css" href="asset/css/style.css"/>
</head>
<body>
<div class="home">
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="">Basic CMS</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto">
                    <?php if($home->isLoggedIn()):?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <?php echo $home->getLoggedInUserName()?>
                            </a>
                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="#">Logout</a>
                            </div>
                        </li>

                    <?php else:?>
                        <li class="nav-item active">
                            <a class="nav-link" href="./admin.php">Admin?</a>
                        </li>
                    <?php endif;?>
                </ul>

            </div>
        </nav>

        <div class="page-body">
            <div class="row">
                <div class="col-md-4">
                    <?php if($pages['status']):?>
                        <ul>
                            <?php foreach ($pages['data'] as $ind => $data){
                                $pageNo = $ind+1;
                                echo "<li><a href='./page.php?id=$data[id]'> Page $pageNo</a></li>";
                            }?>
                        </ul>
                    <?php endif;?>
                </div>

                <div class="col-md-8">
                    <?php if($page['status']):
                        $data = $page['data'];
                        ?>
                        <h1><?php echo $data['page_title']?></h1>
                    <?php endif;?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript" src="asset/js/jquery.min.js"></script>
<script type="text/javascript" src="asset/js/bootstrap.min.js"></script>
</body>
</html>
