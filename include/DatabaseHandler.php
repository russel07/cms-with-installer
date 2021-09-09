<?php
require_once __DIR__.'/BaseClass.php';
require_once __DIR__.'/Auth.php';

class DatabaseHandler extends BaseClass {
    use Auth;

    protected $db_host;
    protected $db_user;
    protected $db_password;
    protected $db_name;
    protected $con = '';

    protected $tableName;
    protected $columns = [];
    protected $conditions = [];
    protected $limit = 0;
    protected $values = [];

    public function __construct($path){
        parent::__construct($path);
        $this->LoadDBInfo();
        $this->connectDb();
    }

    public function LoadDBInfo(){
        $this->db_host = getenv('DB_HOST');
        $this->db_user = getenv('DB_USERNAME');
        $this->db_password = getenv('DB_PASSWORD');
        $this->db_name = getenv('DB_DATABASE');
    }

    public function connectDb(){
        $this->con = new mysqli($this->db_host, $this->db_user,$this->db_password,$this->db_name);
        if(mysqli_connect_error()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }else{
            return $this->con;
        }
    }

    public function getFields(){
        return implode(', ', $this->columns);
    }

    public function getFieldsWithValue(){
        $fields = '';
        foreach ($this->columns as $column => $value){
            if($fields != '')
                $fields .= ', ';

            $fields .= $column.' = '. $value;
        }

        return $fields;
    }

    public function getValues(){
        return implode(', ', $this->values);
    }

    public function getCondition(){
        $condition = '';
        foreach ($this->conditions as $_condition => $val){
            if($condition != '')
                $condition .= ' AND ';
            $value = is_numeric($val) ? $val: "'$val'";
            $condition .= $_condition .'='. $value ;
        }

        return $condition;
    }

    public function prepareGetQuery(){
        $fields = $this->getFields();
        $condition = $this->getCondition();

        $query = "SELECT $fields FROM $this->tableName";
        if($condition)
            $query .=  " WHERE $condition";
        if($this->limit)
            $query .= " Limit $this->limit";
        return $query;
    }

    public function getAll(){
        $query = $this->prepareGetQuery();
        $result = $this->con->query($query);

        if($result->num_rows > 0) {
            $data = array();
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            return array(
                'status' => true,
                'data' => $data
            );
        }else{
            return array(
                'status' => false,
                'message' => 'No record found'
            );
        }
    }

    public function getById(){
        return $this->getOne();
    }

    public function getOne(){
        $this->limit = 1;
        $query = $this->prepareGetQuery();
        $result = $this->con->query($query);

        if($result->num_rows > 0) {
            return array(
                'status' => true,
                'data' => $result->fetch_assoc()
            );
        }else{
            return array(
                'status' => false,
                'message' => 'No record found'
            );
        }
    }

    public function insert()
    {
        $fields = $this->getFields();
        $values = $this->getValues();

        if(!empty($fields) && !empty($values)){
            $sql = "INSERT INTO $this->tableName ($fields) VALUES ($values)";
            if ($this->con->query($sql) === TRUE) {
                return $this->con->insert_id;
            } else {
                return false;
            }
        }
        return false;
    }

    public function update(){
        $fields = $this->getFieldsWithValue();
        $condition = $this->getCondition();
        $sql = "UPDATE $this->tableName SET ";

        if(!empty($fields) && !empty($condition)){
            $query = $sql . $fields . ' WHERE ' .$condition;
            return $this->con->query($query);
        }
        return false;
    }

    public function delete(){
        $condition = $this->getCondition();
        if(!empty($condition)){
            $sql = "DELETE FROM $this->tableName WHERE $condition";

            return $this->con->query($sql);
        }

        return false;
    }
}
