<?php
class Genere{
    // database connection and table name
    private $conn;
    private $table_name = "genere";

    public $id;
    public $tipo;
    public $descrizione;

    public function __construct($db){
        $this->conn = $db;
    }

    function readAll(){

        $query = "SELECT * FROM " . $this->table_name;

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }
    
    function readOneByID(){

        $query = "SELECT * FROM " . $this->table_name. " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id,PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row>0){
            $this->id = $row['id'];
            $this->tipo = $row['tipo'];
            $this->descrizione = $row['descrizione'];
        }
    }

    function readOneByTipo(){

        $query = "SELECT * FROM " . $this->table_name. " WHERE tipo = :tipo LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->tipo=filter_var($this->tipo,FILTER_SANITIZE_STRING);

        $stmt->bindParam(":tipo", $this->tipo,PDO::PARAM_STR);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row>0){
            $this->id = $row['id'];
            $this->tipo = $row['tipo'];
            $this->descrizione = $row['descrizione'];
        }
    }

    function orderByGenere($genere){
        $query = "
        SELECT * FROM ".$this->table_name." 
        LEFT JOIN film ON ".$this->table_name.".genere_id = genere.id 
        HAVING ".$this->table_name.".genere_id = :genere 
        ";

        $stmt = $this->conn->prepare( $query );

        $genere=filter_var($genere,FILTER_SANITIZE_STRING);

        $stmt->bindParam(":genere", $genere,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;
    }
}