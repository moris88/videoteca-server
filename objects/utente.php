<?php
class Utente{

    private $conn;
    private $table_name = "utenti";

    public $id;
    public $nickname;
    public $email;
    public $pwd;
    public $account_id;
    public $data_insert;

    public function __construct($db){
        $this->conn = $db;
    }

    function readOne(){

        $query = "SELECT * FROM ".$this->table_name." WHERE id=:id";

        $stmt = $this->conn->prepare($query);

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row>0){
            $this->id = $row['id'];
            $this->nickname = $row['nickname'];
            $this->email = $row['email'];
            $this->pwd = $row['pwd'];
            $this->account_id = $row['account_id'];
            $this->data_insert = $row['data_insert'];
        }
    }

    function readAll(){

        $query = "SELECT * FROM ".$this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    function write(){

        $query = "INSERT INTO ".$this->table_name."(nickname, email, pwd, account_id) VALUES(:nickname, :email, :pwd, :account_id)";

        $stmt = $this->conn->prepare( $query );

        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->pwd=htmlspecialchars(strip_tags($this->pwd));
        $this->account_id=2;

        $stmt->bindParam(":nickname", $this->nickname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindParam(":pwd", $this->pwd, PDO::PARAM_STR);
        $stmt->bindParam(":account_id", $this->account_id, PDO::PARAM_INT);

        try{
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }catch(Exception $error){
            return false;
        }
    }

    function update(){

        $query = "UPDATE ".$this->table_name." SET nickname=:nickname, email=:email, pwd=:pwd, account_id = :account_id WHERE id=:id";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);
        $this->nickname=htmlspecialchars(strip_tags($this->nickname));
        $this->email=htmlspecialchars(strip_tags($this->email));
        $this->pwd=htmlspecialchars(strip_tags($this->pwd));
        $this->account_id=filter_var($this->account_id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":nickname", $this->nickname, PDO::PARAM_STR);
        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);
        $stmt->bindParam(":pwd", $this->pwd, PDO::PARAM_STR);
        $stmt->bindParam(":account_id", $this->account_id, PDO::PARAM_STR);

        try{
            if($stmt->execute()){
                return true;
            }else{
                return false;
            }
        }catch(Exception $error){
            return false;
        }
    }

    function changeAccount(){

        $query = "UPDATE ".$this->table_name." SET account_id=:account_id WHERE id=:id";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);
        $this->account_id=htmlspecialchars(strip_tags($this->account_id));

        $stmt->bindParam(":id", $this->id, PDO::PARAM_INT);
        $stmt->bindParam(":account_id", $this->account_id, PDO::PARAM_STR);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function delete(){
        $query = "DELETE FROM ".$this->table_name." WHERE id = :id";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id,PDO::PARAM_INT);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function deleteUsers(){
        $query = "DELETE FROM ".$this->table_name." WHERE account_id = 2";
        $stmt = $this->conn->prepare( $query );

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function createHashPassword($pwd){
        $this->pwd = password_hash($pwd, PASSWORD_BCRYPT);
    }

    function createNickname(){
        $this->nickname = uniqid('user_');
    }

    function createPasswordTemp(){
        $pwd_temp = uniqid();
        $this->createHashPassword($pwd_temp);
        return $pwd_temp;
    }

    function verifyPassword(){
        $query = "SELECT pwd FROM ".$this->table_name." WHERE email=:email";
        
        $stmt = $this->conn->prepare( $query );

        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":email", $this->email,PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(password_verify($this->pwd, $row['pwd'])){
            return true;
        }else{
            return false;
        }
    }

    function verifyEmail(){
        $query = "SELECT * FROM ".$this->table_name." WHERE ".$this->table_name.".email=:email";
        
        $stmt = $this->conn->prepare( $query );

        $this->email=htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(":email", $this->email, PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row>0){
            $this->id = $row['id'];
            $this->nickname = $row['nickname'];
            $this->account_id = $row['account_id'];
            $this->data_insert = $row['data_insert'];
            return true;
        }else{
            return false;
        } 
    }
}