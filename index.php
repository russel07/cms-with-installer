<?php

require_once __DIR__.'/include/HomeClass.php';
$env = __DIR__.'/.env';
$home = new HomeClass($env);

if(!$home->isInstalled){
    header("Location:installer.php");
}

$pageId = (isset($_GET['page']) && !empty($_GET['page'])) ? $_GET['page']: '';

$pages = $home->getActivePages();

if($pageId){
    $page = $home->getPagesById($pageId);
    if($page['status']){
        $data = $page['data'];
        $title = $data['page_title'];
        $content = $data['page_content'];
    }else{
        $title = "Content not found";
    }

}else{
    $title = "Home";
}

$loggedIn = $home->isLoggedIn();
$username = $home->getLoggedInUserName();
?>

<?php include './header.php'?>
        <div class="page-body mt-2">
            <div class="row">
                <div class="col-md-2">
                    <?php if($pages['status']):?>
                        <ul class="custom-nav">
                            <?php foreach ($pages['data'] as $ind => $data){
                                $pageNo = $ind+1;
                                echo "<li><a href='./index.php?page=$data[id]'> Page $pageNo</a></li>";
                            }?>
                        </ul>
                    <?php endif;?>
                </div>

                <div class="col-md-10">
                    <?php if(isset($page) && $page['status']):
                        $data = $page['data'];
                        ?>
                        <div class="card">
                            <div class="card-header">
                                <h1><?php echo $data['page_title']?></h1>
                            </div>
                            <div class="card-body">
                                <p><?php echo $data['page_content']?></p>
                            </div>
                        </div>

                    <?php else:
                        if (!$pages['status']): ?>
                            <div class="alert alert-warning">
                                <p class="text-center">No page found. Contact with administrator to create pages</p>
                            </div>
                        <?php else:?>
                            <h1 class="page-title text-center"> Home Page</h1>
                        <?php endif;?>
                    <?php endif;?>
                </div>
            </div>
        </div>

<?php include './footer.php'?>
