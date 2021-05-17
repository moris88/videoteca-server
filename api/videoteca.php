<?php
    include_once '../config/database.php';
    include_once '../objects/film.php';
    include_once '../objects/genere.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $method = $_SERVER['REQUEST_METHOD'];
    if($method == 'OPTIONS'){
        $method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
    }

    $id=0;if(isset($_REQUEST['request'])){$id=filter_var($_REQUEST['request'],FILTER_SANITIZE_NUMBER_INT);}

    $database = new Database();
    $db = $database->getConnection();
    $array_output=array();

    switch ($method) {
        case 'GET':

            $action = 0;
            $search = '';
            if(isset($_REQUEST['titolo'])){ 
                $search = filter_var($_REQUEST['titolo'],FILTER_SANITIZE_STRING);
                $action = 1;
            }else if(isset($_REQUEST['genere'])){
                $search = filter_var($_REQUEST['genere'],FILTER_SANITIZE_STRING);
                $action = 2;
            }else if(isset($_REQUEST['anno'])){
                $search = filter_var($_REQUEST['anno'],FILTER_SANITIZE_STRING);
                $action = 3;
            }else if(isset($_REQUEST['id'])){
                $search = filter_var($_REQUEST['id'],FILTER_SANITIZE_NUMBER_INT);
                $action = 4;
            }else if(isset($_REQUEST['valutazione'])){
                $search = filter_var($_REQUEST['valutazione'],FILTER_SANITIZE_NUMBER_INT);
                $action = 5;
            }

            switch($action){
                case 1: //ordina per titolo
                    $film = new Film($db);
                    $film->titolo = '%'.$search.'%';
                    $stmt = $film->queryTitolo();              
                    $num = $stmt->rowCount();
                    if($num>0){

                        $array_output["records"]=array();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);

                            $films_item = array(
                                "id" =>  $row['id'],
                                "titolo" => $row['titolo'],
                                "genere" => $row['tipo'],
                                "durata" => $row['durata'],
                                "valutazione" => $row['valutazione'],
                            );
                            array_push($array_output["records"], $films_item);
                        }
                    }        
                    break;
                case 2: //ordina per genere
                    $film = new Film($db);
                    $film->genere_id = date($search);
                    $stmt = $film->queryGenere();              
                    $num = $stmt->rowCount();
                    if($num>0){

                        $array_output["records"]=array();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);

                            $films_item = array(
                                "id" =>  $row['id'],
                                "titolo" => $row['titolo'],
                                "genere" => $row['tipo'],
                                "durata" => $row['durata'],
                                "valutazione" => $row['valutazione'],
                            );
                            array_push($array_output["records"], $films_item);
                        }
                    }      
                    break;
                case 3: //ordina per anno
                    $film = new Film($db);
                    $film->anno = date($search);
                    $stmt = $film->queryAnno();              
                    $num = $stmt->rowCount();
                    if($num>0){

                        $array_output["records"]=array();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);

                            $films_item = array(
                                "id" =>  $row['id'],
                                "titolo" => $row['titolo'],
                                "genere" => $row['tipo'],
                                "durata" => $row['durata'],
                                "valutazione" => $row['valutazione'],
                            );
                            array_push($array_output["records"], $films_item);
                        }
                    }        
                    break;
                case 4:
                    if($search>0){
                        $film = new Film($db);
                        $film->id = $search;
                        $film->readOneByID();              
                        
                        if($film->titolo != NULL){
                            $array_output["records"]=array();
                            $films_item = array(
                                "id" =>  $film->id,
                                "titolo" => $film->titolo,
                                "genere" => $film->genere_id,
                                "anno" => $film->anno,
                                "valutazione" => $film->valutazione,
                                "trama" => $film->trama,
                                "locandina" => $film->locandina,
                                "posizione" => $film->posizione,
                                "trailer" => $film->trailer,
                                "durata" => $film->durata,
                                "data_insert" => $film->data_insert,
                            );
                            array_push($array_output["records"], $films_item);
                        }

                    }else{
                        $film = new Film($db);

                        $stmt = $film->readAll();
                        $num = $stmt->rowCount();
                        if($num>0){

                            $array_output["records"]=array();
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                                extract($row);

                                $films_item = array(
                                    "id" =>  $row['id'],
                                    "titolo" => $row['titolo'],
                                    "genere" => $row['tipo'],
                                    "durata" => $row['durata'],
                                    "valutazione" => $row['valutazione'],
                                );
                                array_push($array_output["records"], $films_item);
                            }
                        }
                    }
                    break;
                case 5: //ordina per valutazione
                    $film = new Film($db);
                    $film->valutazione = $search;
                    $stmt = $film->queryValutazione();              
                    $num = $stmt->rowCount();
                    if($num>0){

                        $array_output["records"]=array();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                            extract($row);

                            $films_item = array(
                                "id" =>  $row['id'],
                                "titolo" => $row['titolo'],
                                "genere" => $row['tipo'],
                                "durata" => $row['durata'],
                                "valutazione" => $row['valutazione'],
                            );
                            array_push($array_output["records"], $films_item);
                        }
                    }
                    break;
                case 0:
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Errore nell'url.")
                    );
                    break;
            }

            //RESPONSE
            if(array_key_exists('records',$array_output)){
                http_response_code(200);
                print_r(json_encode($array_output));
            }else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Nessun dato cercato.")
                );
            }
            break;  
        case 'POST':
            $film = new Film($db);
            $data = json_decode(file_get_contents("php://input"));

            $film->titolo = $data->titolo;
            $film->genere_id = $data->genere;
            $film->trama = $data->trama;
            $film->valutazione = $data->valutazione;
            if($data->anno === ''){
                $film->anno = date('Y');
            }else{
                $film->anno = $data->anno;
            }
            $film->locandina = $data->locandina;
            $film->posizione = $data->posizione;
            $film->trailer = $data->trailer;
            $film->durata = $data->durata;
            $film->data_insert = date('Y-m-d H:i:s');

            //RESPONSE
            if($film->write()){
                http_response_code(200);
                echo json_encode(
                    array("message" => "Film inserito.")
                );
            }
            else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Impossibile scrivere il film.")
                );
            }
            break;
        case 'PUT':
    
            $film = new Film($db);
            $data = json_decode(file_get_contents("php://input"));

            $film->id = $data->id;
            $film->titolo = $data->titolo;
            $film->genere_id = $data->genere;
            $film->trama = $data->trama;
            $film->valutazione = $data->valutazione;
            if($data->anno === ''){
                $film->anno = date('Y');
            }else{
                $film->anno = $data->anno;
            }
            $film->locandina = $data->locandina;
            $film->posizione = $data->posizione;
            $film->trailer = $data->trailer;
            $film->durata = $data->durata;
            $film->data_insert = date('Y-m-d H:i:s');

            //RESPONSE
            if($film->update()){
                http_response_code(200);
                echo json_encode(
                    array("message" => "Film modificato.")
                );
            }
            else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Impossibile aggiornare il film.")
                );
            }
            break;
        case 'DELETE':

            $film = new film($db);
            $film->id = $id;

            if($id == 0){
                //RESPONSE
                if($film->deletes()){
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Film eliminati.")
                    );
                }
                else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Impossibile eliminare i film.")
                    );
                }
            }
            if($id > 0){
                //RESPONSE
                if($film->delete()){
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Film eliminato.")
                    );
                }
                else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Impossibile cancellare il film.")
                    );
                }
            }else{
                echo json_encode(
                    array("message" => "No id.")
                );
            }
            break;
    }