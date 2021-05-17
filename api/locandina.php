<?php
    include_once '../config/database.php';
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

    switch ($method) {
        case 'POST':
            if(!empty($_FILES['file'])){
                $path = "../../assets/image/";
                $path = $path.basename($_FILES['file']['name']);

                if(!file_exists($path)){
                    if(move_uploaded_file($_FILES['file']['tmp_name'], $path)){
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Caricamento effettuato.")
                        );
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Errore nel caricamento del file!")
                        );
                    }
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Il file è già presente.")
                    );
                }
            }
            break;
        case 'DELETE':

            $film = new Film($db);
            $film->id = $id;
            $film->readOneByID();              
            $path = "../../assets/image/".$film->locandina;           
            
            if(file_exists($path)){
                if(unlink($path)){
                    $film->deleteLocandina();
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Eliminazione file effettuata.")
                    );
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Impossibile eliminare nessun file presente.")
                    );
                }
            }
            break;
    }
?>