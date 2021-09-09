<?php

require_once __DIR__.'/DatabaseHandler.php';

class HomeClass extends DatabaseHandler {
    use Auth;
    public function __construct($path){
        parent::__construct($path);
    }

    public function getActivePages()
    {
        $this->tableName = 'pages';
        $this->columns = array('id', 'page_title', 'page_content', 'page_status');
        $this->conditions = array('page_status' => 'Active');

        return $this->getAll();
    }

    public function getAllPages()
    {
        $this->tableName = 'pages';
        $this->columns = array('id', 'page_title', 'page_content', 'page_status');

        return $this->getAll();
    }

    public function getPagesById($id){
        $this->tableName = 'pages';
        $this->columns = array('id', 'page_title', 'page_content', 'page_status');
        $this->conditions = array('id'=> $id);

        return $this->getById();
    }

    public function validatePageForm($post){
        $arr = [];
        if($post['page_title'] == ''){
            array_push($arr, "Page title can not be blank");
        }
        if($post['page_content'] == ''){
            array_push($arr, "Page content can not be blank");
        }

        return $arr;
    }

    public function createPage($post){
        $this->tableName = 'pages';
        $page_title = $this->con->real_escape_string($post['page_title']);
        $page_content = nl2br($this->con->real_escape_string($post['page_content']));

        $this->columns = array('page_title', 'page_content', 'page_status');
        $this->values = array( "'$page_title'", "'$page_content'", "'Active'");

        return $this->insert();
    }

    public function updatePage($post){
        $this->tableName = 'pages';
        $id = $this->con->real_escape_string($post['id']);
        $page_title = $this->con->real_escape_string($post['page_title']);
        $page_content = nl2br($this->con->real_escape_string($post['page_content']));
        $page_status = $this->con->real_escape_string($post['page_status']);
        $this->columns = array('page_title' => "'$page_title'", 'page_content' => "'$page_content'", 'page_status'=>"'$page_status'");
        $this->conditions = array( 'id' => $id);

        return $this->update();
    }

    public function deletePage($id){
        $this->tableName = 'pages';
        $this->conditions = array( 'id' => $id);

        return $this->delete();
    }
}
