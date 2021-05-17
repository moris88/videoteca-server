<?php
class Film{

    private $conn;
    private $table_name = "film";

    public $id;
    public $titolo;
    public $genere_id;
    public $trama;
    public $valutazione;
    public $anno;
    public $locandina;
    public $posizione;
    public $trailer;
    public $durata;
    public $data_insert;

    public function __construct($db){
        $this->conn = $db;
    }

    function readAll(){

        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".durata, ".$this->table_name.".valutazione FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id ORDER BY ".$this->table_name.".id DESC";

        $stmt = $this->conn->prepare($query);

        $stmt->execute();

        return $stmt;
    }

    function readOneByID(){

        $query = "SELECT * FROM " . $this->table_name. " WHERE id = :id LIMIT 0,1";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id,PDO::PARAM_INT);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if($row>0){
            $this->id = $row['id'];
            $this->titolo = $row['titolo'];
            $this->genere_id = $row['genere_id'];
            $this->trama = $row['trama'];
            $this->valutazione = $row['valutazione'];
            $this->anno = $row['anno'];
            $this->locandina = $row['locandina'];
            $this->posizione = $row['posizione'];
            $this->trailer = $row['trailer'];
            $this->durata = $row['durata'];
            $this->data_insert = $row['data_insert'];
        }
    }

    function queryTitolo(){

        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".valutazione, ".$this->table_name.".durata FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id WHERE ".$this->table_name.".titolo  LIKE :titolo ORDER BY ".$this->table_name.".id";

        $stmt = $this->conn->prepare( $query );

        $this->titolo=filter_var($this->titolo,FILTER_SANITIZE_STRING);

        $stmt->bindParam(":titolo", $this->titolo,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;
    }

    function queryAnno(){

        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".valutazione, ".$this->table_name.".durata FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id WHERE ".$this->table_name.".anno = :anno ORDER BY ".$this->table_name.".id";

        $stmt = $this->conn->prepare( $query );

        $stmt->bindParam(":anno", $this->anno);

        $stmt->execute();

        return $stmt;
    }

    function queryGenere(){

        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".valutazione, ".$this->table_name.".durata FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id WHERE ".$this->table_name.".genere_id = :genere_id ORDER BY ".$this->table_name.".id";

        $stmt = $this->conn->prepare( $query );

        $stmt->bindParam(":genere_id", $this->genere_id);

        $stmt->execute();

        return $stmt;
    }

    function queryValutazione(){

        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".valutazione, ".$this->table_name.".durata FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id HAVING ".$this->table_name.".valutazione = :valutazione ORDER BY ".$this->table_name.".id";

        $stmt = $this->conn->prepare( $query );

        $this->valutazione=filter_var($this->valutazione,FILTER_SANITIZE_STRING);

        $stmt->bindParam(":valutazione", $this->valutazione,PDO::PARAM_STR);

        $stmt->execute();

        return $stmt;
    }

    function write(){

        $query = "INSERT INTO ".$this->table_name."(titolo, genere_id, trama, valutazione, anno, locandina, posizione, trailer, durata) VALUES(:titolo, :genere_id, :trama, :valutazione, :anno, :locandina, :posizione, :trailer, :durata)";

        $stmt = $this->conn->prepare( $query );

        $this->titolo=htmlspecialchars(strip_tags($this->titolo));
        $this->genere_id=filter_var($this->genere_id,FILTER_SANITIZE_NUMBER_INT);
        $this->trama=htmlspecialchars(strip_tags($this->trama));
        $this->valutazione=filter_var($this->valutazione,FILTER_SANITIZE_NUMBER_INT);
        $this->anno=htmlspecialchars(strip_tags($this->anno));
        $this->locandina=htmlspecialchars(strip_tags($this->locandina));
        $this->posizione=htmlspecialchars(strip_tags($this->posizione));
        $this->trailer=htmlspecialchars(strip_tags($this->trailer));
        $this->durata=filter_var($this->durata,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":genere_id", $this->genere_id);
        $stmt->bindParam(":trama", $this->trama);
        $stmt->bindParam(":valutazione", $this->valutazione);
        $stmt->bindParam(":anno", $this->anno);
        $stmt->bindParam(":locandina", $this->locandina);
        $stmt->bindParam(":posizione", $this->posizione);
        $stmt->bindParam(":trailer", $this->trailer);
        $stmt->bindParam(":durata", $this->durata);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function update(){

        $query = "UPDATE ".$this->table_name." SET titolo=:titolo, genere_id=:genere_id, trama=:trama, valutazione=:valutazione, anno=:anno, locandina=:locandina, posizione=:posizione, trailer=:trailer, durata=:durata, data_insert=:data_insert WHERE id=:id";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);
        $this->titolo=htmlspecialchars(strip_tags($this->titolo));
        $this->genere_id=filter_var($this->genere_id,FILTER_SANITIZE_NUMBER_INT);
        $this->trama=htmlspecialchars(strip_tags($this->trama));
        $this->valutazione=filter_var($this->valutazione,FILTER_SANITIZE_NUMBER_INT);
        $this->anno=htmlspecialchars(strip_tags($this->anno));
        $this->locandina=htmlspecialchars(strip_tags($this->locandina));
        $this->posizione=htmlspecialchars(strip_tags($this->posizione));
        $this->trailer=htmlspecialchars(strip_tags($this->trailer));
        $this->durata=filter_var($this->durata,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id);
        $stmt->bindParam(":titolo", $this->titolo);
        $stmt->bindParam(":genere_id", $this->genere_id);
        $stmt->bindParam(":trama", $this->trama);
        $stmt->bindParam(":valutazione", $this->valutazione);
        $stmt->bindParam(":anno", $this->anno);
        $stmt->bindParam(":locandina", $this->locandina);
        $stmt->bindParam(":posizione", $this->posizione);
        $stmt->bindParam(":trailer", $this->trailer);
        $stmt->bindParam(":durata", $this->durata);
        $stmt->bindParam(":data_insert", $this->data_insert);  

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

    function deletes(){
        $query = "DELETE FROM ".$this->table_name;

        $stmt = $this->conn->prepare( $query );

        $stmt->execute();

        $query = "ALTER TABLE ".$this->table_name." AUTO_INCREMENT = 1";

        $stmt = $this->conn->prepare( $query );

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function deleteLocandina(){
        $query = "UPDATE ".$this->table_name." SET locandina = '' WHERE id = :id";

        $stmt = $this->conn->prepare( $query );

        $this->id=filter_var($this->id,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":id", $this->id);

        if($stmt->execute()){
            return true;
        }else{
            return false;
        }
    }

    function novita($limit){
        $query = "SELECT ".$this->table_name.".id, ".$this->table_name.".titolo, genere.tipo, ".$this->table_name.".locandina, ".$this->table_name.".durata FROM ".$this->table_name." INNER JOIN genere ON ".$this->table_name.".genere_id = genere.id ORDER BY ".$this->table_name.".data_insert DESC LIMIT 0,:limit";

        $stmt = $this->conn->prepare( $query );

        $limit=filter_var($limit,FILTER_SANITIZE_NUMBER_INT);

        $stmt->bindParam(":limit", $limit,PDO::PARAM_INT);

        $stmt->execute();

        return $stmt;
    }
}