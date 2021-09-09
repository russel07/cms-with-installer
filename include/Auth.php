<?php
session_start();
trait Auth {
    function login($post){
        $email = '';
        $password = '';
        $arr = [];
        if($post['email'] == ''){
            array_push($arr, "Email address can not be blank");
        }elseif(!filter_var($post['email'], FILTER_VALIDATE_EMAIL)) {
            array_push($arr, "Provided Email address is invalid");
        }else{
            $email = $post['email'];
        }

        if($post['password'] == ''){
            array_push($arr, "Password can not be blank");
        }else{
            $password = $post['password'];
        }

        if(sizeof($arr))
            return array(
                "status" => false,
                "error" => $arr
            );

        $hashPassword = md5($password);

        $this->tableName = 'users';
        $this->columns = array('id', 'name', 'email_address', 'user_type');
        $this->conditions = array('email_address' => $email, 'password' => $hashPassword);

        $row = $this->getOne();

        if($row['status']){
            $_SESSION['users'] = $row['data'];
            return array(
                "status" => true,
                "user" => $_SESSION['users']
            );
        }else{
            array_push($arr, "Provided credential is invalid");
            return array(
                "status" => false,
                "error" => $arr
            );
        }
    }

    function logout(){
        unset($_SESSION['users']);
        session_unset();
        session_destroy();

        return true;
    }

    function isLoggedIn(){
        if(isset($_SESSION['users']) && !empty($_SESSION['users']))
            return true;

        return false;
    }

    function getLoggedInUserInfo(){
        if(isset($_SESSION['users']) && !empty($_SESSION['users'])){
            return $_SESSION['users'];
        }else{
            return [];
        }
    }

    function getLoggedInUserType(){
        if(isset($_SESSION['users']) && !empty($_SESSION['users'])){
            return $_SESSION['users']['user_type'];
        }else{
            return false;
        }
    }

    function getLoggedInUserName(){
        $userInfo = $this->getLoggedInUserInfo();
        if(!empty($userInfo)){
            if(isset($userInfo['name']) && $userInfo['name'])
                return  $userInfo['name'];
            else return  "";
        }else return "";
    }
}
