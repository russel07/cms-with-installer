<?php
require_once __DIR__.'/include/InstallerClass.php';
$env = __DIR__.'/.env';
$installer = new InstallerClass($env);

if($installer->isInstalled){
    header("Location:index.php");
}
$err = [];
if(isset($_REQUEST) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (isset($_POST) && !empty($_POST)) {
        $err = $installer->vaildateDBCridential($_POST);

        if(!sizeof($err)){
            $install = $installer->install($_POST);
            if(is_array($install) && !$install['status']){
                $msg = $install['message'];
                array_push($arr, $msg);
            }else{
                header("Location:index.php");
            }

        }
    }
}

$title = "Installer";
$loggedIn = false;
$installer = true;
?>
<?php include './header.php'?>
    <div class="col-md-12">
        <h1 class="text-center page-title">Installer</h1>
    </div>
    <div class="page-body mt-2">
        <div class="card col-md-8 offset-2 mt-2">
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
                <h2>Database Credential:</h2>
                <hr/>
                <div class="form-group">
                    <label for="db_host">Database Host:</label>
                    <input type="text" name="db_host" class="form-control" id="db_host" required>
                </div>
                <div class="form-group">
                    <label for="db_user">Database Username:</label>
                    <input type="text" name="db_username" class="form-control" id="db_user" required>
                </div>
                <div class="form-group">
                    <label for="db_password">Database Password:</label>
                    <input type="text" name="db_password" class="form-control" id="db_password">
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name:</label>
                    <input type="text" name="db_database" class="form-control" id="db_name" required>
                </div>



                <h2>System Administrator:</h2><hr/>
                <div class="form-group">
                    <label for="admin_name">Admin Name:</label>
                    <input type="text" name="admin_name" class="form-control" id="admin_name" required>
                </div>
                <div class="form-group">
                    <label for="admin_email">Admin Email:</label>
                    <input type="text" name="admin_email" class="form-control" id="admin_email" required>
                </div>
                <div class="form-group">
                    <label for="admin_password">Admin Password:</label>
                    <input type="password" name="admin_password" class="form-control" id="admin_password">
                </div>


                <div class="form-group text-center">
                    <button type="submit" class="btn btn-outline-success">Install</button>
                </div>
            </form>
        </div>
    </div>
<?php include './footer.php'?>
