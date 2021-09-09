<?php
require_once __DIR__.'/DatabaseHandler.php';

class InstallerClass extends DatabaseHandler {
    public function __construct($path){
        parent::__construct($path);
    }

    protected $connected = false;

    function connectDatabase(){
        /*$driver = new mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ERROR;
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);*/
        try{
            $this->con = new mysqli($this->db_host, $this->db_user, $this->db_password, $this->db_name);
            $this->connected = true;
        }catch(mysqli_sql_exception $e){
            $this->connected = false;
        }

        return $this->con;
    }

    function checkHost($host) {
        $ip = gethostbyname("$host");

        return filter_var($ip, FILTER_VALIDATE_IP);
    }

    public function vaildateDBCridential($post){
        $arr = [];
        $this->db_password = $post['db_password'];
        if($post['db_host'] == ''){
            array_push($arr, "Database host can not be blank");
        }else{
            $this->db_host = $post['db_host'];
        }
        if($post['db_username'] == ''){
            array_push($arr, "Database user can not be blank");
        }else{
            $this->db_user = $post['db_username'];
        }
        if($post['db_database'] == ''){
            array_push($arr, "Database name can not be blank");
        }else{
            $this->db_name = $post['db_database'];
        }

        if(!$this->checkHost($this->db_host)){
            array_push($arr, "Provided host name is invalid");
        }

        $this->connectDatabase();
        if($this->connected){
            if(!$this->con->select_db($this->db_name)){
                array_push($arr, "Database name does not exist");
            }

        }else{
            array_push($arr, "Database credential is invalid");
        }

        return $arr;
    }

    public function install($post){
        foreach ($post as $name => $value){
            $this->setEnv(strtoupper($name), $value);
        }
        $this->connectDb();
        $this->setEnv('INSTALLATION', 'DONE');

        $tables = $this->createTable();

        if(is_array($tables) && !$tables['status']){
            return array(
                'status' => false,
                'message' => 'Installation failed '.$tables['message']
            );
        }

        $seed = $this->createUser($post['admin_name'],$post['admin_email'], $post['admin_password'], 'Admin');
        if(is_array($seed) && !$seed['status']){
            return array(
                'status' => false,
                'message' => 'Installation failed '.$seed['message']
            );
        }

        $this->isInstalled();

        return true;
    }

    public function createTable(){
        $users = "CREATE TABLE IF NOT EXISTS `users` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `name` VARCHAR(50) NOT NULL , 
            `email_address` VARCHAR(100) NOT NULL , 
            `password` VARCHAR(256) NOT NULL , 
            `user_type` ENUM('Admin','User') NOT NULL DEFAULT 'User' , 
            PRIMARY KEY (`id`)) ENGINE = InnoDB AUTO_INCREMENT=1;";

        if($this->con->query($users) !== true){
            return array(
                'status' => false,
                'message' => 'Unable to create users table'
            );
        }

        $pages = "CREATE TABLE IF NOT EXISTS  `pages` ( 
            `id` INT NOT NULL AUTO_INCREMENT , 
            `page_title` VARCHAR(200) NOT NULL , 
            `page_content` TEXT NOT NULL , 
            `page_status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active' , 
            PRIMARY KEY (`id`)) ENGINE = InnoDB AUTO_INCREMENT=1;";

        if($this->con->query($pages) !== true){
            return array(
                'status' => false,
                'message' => 'Unable to create pages table'
            );
        }

        return true;
    }

    public function createUser($name, $email, $password, $type){
        if(!$this->getUserByEmail($email)){
            $password = md5($password);

            $query = "INSERT INTO `users` (`name`, `email_address`, `password`, `user_type`) VALUES ('$name', '$email', '$password', '$type')";
            $row = $this->con->query($query);

            if($row === true){
                return array(
                    'status' => true,
                    'id' => $this->con->insert_id
                );
            }else{
                return array(
                    'status' => false,
                    'message' => "Unable to insert row, try again"
                );
            }
        }

        return array(
            'status' => false,
            'message' => 'User already exist with this email'
        );
    }

    public function getUserByEmail($email){
        $result = $this->con->query("SELECT * FROM `users` where email_address = '$email' Limit 1");

        if($result->num_rows > 0){
            return $result->fetch_assoc();
        }

        return false;
    }
}
