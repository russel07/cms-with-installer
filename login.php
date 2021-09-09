<?php
require_once __DIR__.'/include/HomeClass.php';
$env = __DIR__.'/.env';
$home = new HomeClass($env);

if(!$home->isInstalled){
    header("Location:installer.php");
}
$loggedIn = $home->isLoggedIn();
$username = $home->getLoggedInUserName();

if($loggedIn){
    header("Location:index.php");
}

$err = [];
if(isset($_REQUEST) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (isset($_POST) && !empty($_POST)) {
        $login = $home->login($_POST);

        if(!$login['status']){
            $err = $login['error'];
        }else{
            header("Location:admin.php");
        }
    }
}

?>

<?php include './header.php'?>
<div class="page-body mt-2">
    <div class="row">
        <div class="col-md-12">
            <h1 class="text-center page-title">Login to explore</h1>
        </div>

        <div class="card col-md-8 offset-2 mt-2 p-2">
            <?php
                if(sizeof($err) > 0):
            ?>
                <div class="alert alert-danger">
                    <p>please fix the following issue(s)</p>
                    <ul>
                        <?php foreach ($err as $error){
                            echo "<li>$error</li>";
                        }?>
                    </ul>
                </div>
            <?php $err= []; endif;?>

            <form action="" method="POST" class="p-5">
                <div class="form-group">
                    <label for="admin_email">Email:</label>
                    <input type="text" name="email" class="form-control" id="admin_email" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Admin Password:</label>
                    <input type="password" name="password" class="form-control" id="admin_password">
                </div>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-outline-success">Login</button>
                </div>
            </form>
        </div>
    </div>
</div>

 <?php include './footer.php'?>
