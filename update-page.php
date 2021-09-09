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

$title = "Update page";

$err = [];
if(isset($_REQUEST) && ($_SERVER['REQUEST_METHOD'] === 'POST')) {
    if (isset($_POST) && !empty($_POST)) {
        $home->validatePageForm($_POST);

        if(sizeof($err) < 1){
            $page = $home->updatePage($_POST);
            if($page){
                header("Location:admin.php");
            }else{
                array_push($err, 'Something went wrong try again');
            }
        }
    }
}

$page = $home->getPagesById($pageId);

if($page['status']){
    $data = $page['data'];
    $title = $data['page_title'];
}else{
    $title = "Content not found";
}

?>

<?php include './header.php'?>
    <div class="page-body mt-2">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center page-title">Update page</h1>
            </div>

            <div class="card col-md-10 mt-2 offset-1">
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

                <form action="" method="POST">
                    <input type="hidden" name="id" value="<?php if(isset($data['id'])) echo $data['id']; ?>"/>

                    <div class="form-group">
                        <label for="page_title">Page Title:</label>
                        <input type="text" name="page_title" class="form-control" value="<?php if(isset($data['page_title'])) echo $data['page_title']; ?>"  id="page_title" maxlength="200" required>
                    </div>

                    <div class="form-group">
                        <label for="page_content">Page Content:</label>
                        <!-- Include the Quill library -->
                        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
                        <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
                        <div id="toolbar">
                            <!-- Add buttons as you would before -->
                            <button class="ql-bold"></button>
                            <button class="ql-italic"></button>
                            <button class="ql-list" value="ordered"></button>
                            <button class="ql-list" value="bullet"></button>
                            <button class="ql-link"></button>
                            <button class="ql-image"></button>
                        </div>
                        <div id="editor"><?php if(isset($data['page_content'])) echo $data['page_content']; ?></div>
                        <input type="hidden" class="form-control" name="page_content" id="page_content">
                    </div>

                    <script>
                        var quill2 = new Quill('#editor', {
                            placeholder: 'Compose your order details',
                            theme: 'snow',
                            modules: {
                                toolbar: '#toolbar'
                            }
                        });

                        function setPageContent(){console.log("here");
                            var html = quill2.root.innerHTML;
                            document.getElementById("page_content").value = html;

                            return true;
                        }
                    </script>

                    <div class="form-group">
                        <label for="page_title">Page Status:</label>
                        <select name="page_status" class="form-control"id="page_title">
                            <option value="Active" <?php if(isset($data['page_status']) && $data['page_status'] === 'Active') echo "selected"; ?>>Active</option>
                            <option value="Inactive" <?php if(isset($data['page_status']) && $data['page_status'] === 'Inactive') echo "selected"; ?>>Inactive</option>
                        </select>
                   </div>

                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-outline-success" id="create_page" onclick="return setPageContent()">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<?php include './footer.php'?>
