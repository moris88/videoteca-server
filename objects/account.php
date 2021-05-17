<?php
class Account{
    // database connection and table name
    private $conn;
    private $table_name = "account";

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
}