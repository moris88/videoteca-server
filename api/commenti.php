<?php
    include_once '../config/database.php';
    include_once '../objects/commento.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $method = $_SERVER['REQUEST_METHOD'];
    if($method == 'OPTIONS'){
        $method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
    }

    $database = new Database();
    $db = $database->getConnection();
    $array_output=array();

    switch ($method) {
        case 'GET':
            $commento = new Commento($db);

            $stmt = $commento->read();
            $num = $stmt->rowCount();
            if($num>0){

                $array_output["commenti"]=array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                    extract($row);

                    $commenti_item = array(
                        "id" =>  $row['id'],
                        "utente" => $row['nickname'],
                        "commento" => $row['commento'],
                    );
                    array_push($array_output["commenti"], $commenti_item);
                }
                //RESPONSE
                if(array_key_exists('commenti',$array_output)){
                    http_response_code(200);
                    print_r(json_encode($array_output));
                }else{
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Nessun commento inserito.")
                    );
                }
            }else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Nessun utente registrato.")
                );
            }
            break;
        case 'POST':  
            $commento = new Commento($db);
            $data = json_decode(file_get_contents("php://input"));

            $commento->id = date('Y-m-d H:i:s');
            $commento->utente_id = $data->utente;
            $commento->commento = $data->commento;

            //RESPONSE
            if($commento->write()){
                http_response_code(200);
                echo json_encode(
                    array("message" => "Commento inserito.")
                );
            }
            else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Impossibile scrivere il commento.")
                );
            }
            break;
    }

?>