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
            $film = new Film($db);
            $stmt = $film->novita($id);              
            
            $num = $stmt->rowCount();
            if($num>0){

                $array_output["records"]=array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);

                    $films_item = array(
                        "id" =>  $row['id'],
                        "titolo" => $row['titolo'],
                        "genere" => $row['tipo'],
                        "locandina" => $row['locandina'],
                        "durata" => $row['durata'],
                    );
                    array_push($array_output["records"], $films_item);
                }
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
            break;
    }






?>