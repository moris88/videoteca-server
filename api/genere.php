<?php
    include_once '../config/database.php';
    include_once '../objects/genere.php';
    include_once '../objects/film.php';

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

            $genere = new Genere($db);
            if($id==0){
                $stmt = $genere->readAll();
                $array_output["records"]=array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);

                    $genere_item = array(
                        "id" =>  $row['id'],
                        "tipo" => $row['tipo'],
                        "descrizione" => $row['descrizione'],
                    );
                    array_push($array_output["records"], $genere_item);
                }
            }else{
                $genere->id = $id;
                $genere->readOneByID();
                if($genere->tipo!==NULL){
                    $array_output["records"]=array();
                    $genere_item = array(
                        "id" =>  $genere->id,
                        "tipo" => $genere->tipo,
                        "descrizione" => $genere->descrizione,
                    );
                    array_push($array_output["records"], $genere_item);
                }
            }

            break;
    }

    //RESPONSE
    if(array_key_exists('records',$array_output)){
        http_response_code(200);
        print_r(json_encode($array_output));
    }else{
        http_response_code(500);
        echo json_encode(
            array("message" => "Nessun dato trovato.")
        );
    }
?>