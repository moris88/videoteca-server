<?php
class Commento{

    private $conn;
    private $table_name = "commenti";

    public $id;
    public $utente_id;
    public $commento;

    public function __construct($db){
        $this->conn = $db;
    }

    function read(){

        $query = "SELECT ".$this->table_name.".id, utenti.nickname, ".$this->table_name.".commento FROM ".$this->table_name." INNER JOIN utenti ON ".$this->table_name.".utente_id=utenti.id ORDER BY ".$this->table_name.".id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    function write(){

        $query = "INSERT INTO ".$this->table_name."(utente_id, commento) VALUES(:utente_id, :commento)";

        $stmt = $this->conn->prepare( $query );

        $this->commento=htmlspecialchars(strip_tags($this->commento));
        $this->utente_id=filter_var($this->utente_id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":utente_id", $this->utente_id, PDO::PARAM_INT);
        $stmt->bindParam(":commento", $this->commento, PDO::PARAM_STR);

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
}