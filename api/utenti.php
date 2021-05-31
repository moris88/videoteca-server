<?php
    include_once '../config/database.php';
    include_once '../objects/account.php';
    include_once '../objects/utente.php';
    include_once '../objects/film.php';
    include_once '../objects/email.php';

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: *");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

    $method = $_SERVER['REQUEST_METHOD'];
    if($method == 'OPTIONS'){
        $method = $_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'];
    }

    $action='';if(isset($_REQUEST['action'])){$action = filter_var($_REQUEST['action'],FILTER_SANITIZE_STRING);}

    $database = new Database();
    $db = $database->getConnection();
    $array_output=array();

    switch ($method) {
        case 'GET':
            $id = (int)$action; 
            if($id>0){
                $utente = new Utente($db);

                $utente->id = $id;
                $stmt = $utente->readOne();
                
                if($utente->email != NULL){
                    $array_output["records"]=array();
                    $utenti_item = array(
                        "id" =>  $utente->id,
                        "nickname" =>  $utente->nickname,
                        "email" => $utente->email,
                        "pwd" => $utente->pwd,
                    );
                    array_push($array_output["records"], $utenti_item);
                }
                //RESPONSE
                if(array_key_exists('records',$array_output)){
                    http_response_code(200);
                    print_r(json_encode($array_output));
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Nessun utente trovato.")
                    );
                }
            }
            else if($id===0){
                $utente = new Utente($db);

                $stmt = $utente->readAll();
                $num = $stmt->rowCount();
                if($num>0){

                    $array_output["records"]=array();
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                        extract($row);

                        $utenti_item = array(
                            "id" =>  $row['id'],
                            "nickname" => $row['nickname'],
                            "email" => $row['email'],
                            "account_id" => $row['account_id'],
                            "data_insert" => $row['data_insert'],
                        );
                        array_push($array_output["records"], $utenti_item);
                    }
                    //RESPONSE
                    if(array_key_exists('records',$array_output)){
                        http_response_code(200);
                        print_r(json_encode($array_output));
                    }else{
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Nessun dato cercato.")
                        );
                    }
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Nessun utente registrato.")
                    );
                }
            }else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Errore nella richieta.")
                );
            }
            break;
        case 'POST':  
            switch($action){
                case 'login':
                    $utente = new Utente($db);
                    $data = json_decode(file_get_contents("php://input"));
                    $utente->email = $data->email;
                    $utente->pwd = $data->pwd;
                    if($utente->verifyEmail()){
                        if($utente->verifyPassword()){                           
                            http_response_code(200);
                            echo json_encode(
                                array(
                                    "message" => "Login effettuato.",
                                    "id" => "$utente->id",
                                    "nickname" => "$utente->nickname",
                                    "account_id" => "$utente->account_id",
                                    "data_insert" => "$utente->data_insert",
                                )
                            );
                        }else{
                            http_response_code(500);
                            echo json_encode(
                                array("message" => "Errore nella password.")
                            );
                        }
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Nessun utente con questa email.")
                        );
                    }
                    break;
                case 'register':
                    $utente = new Utente($db);
                    $data = json_decode(file_get_contents("php://input"));
                    $utente->createNickname();
                    $utente->email = $data->email;
                    $utente->createHashPassword($data->pwd);

                    if($utente->write()){
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Registrazione creata.")
                        );
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Errore registrazione, email già presente!.")
                        );
                    }
                    break;
                case 'newsletter':
                    
                    $utente = new Utente($db);

                    $stmtUtenti = $utente->readAll();
                    $num = $stmtUtenti->rowCount();
                    if($num>0){
                        while ($rowUtente = $stmtUtenti->fetch(PDO::FETCH_ASSOC)){
                            extract($rowUtente);
                            $film = new Film($db);
                            $stmtFilm = $film->novita(3);              
                            $films_items = array();
                            $num = $stmtFilm->rowCount();
                            if($num>0){
                                $films_items["films"]=array();
                                while ($rowFilm = $stmtFilm->fetch(PDO::FETCH_ASSOC)){
                                    extract($rowFilm);
            
                                    $film_item = array(
                                        "titolo" => $rowFilm['titolo'],
                                        "genere" => $rowFilm['tipo'],
                                        "durata" => $rowFilm['durata'],
                                        "trama" => $rowFilm['trama'],
                                    );
                                    array_push($films_items["films"], $film_item);
                                }
                            }
                            //TO DO EMAIL SEND
                            send_newsletter($rowUtente,$films_items);
                        }
                        //RESPONSE
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Email inviate.")
                        );
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Nessun utente registrato.")
                        );
                    }
                    break; 
                case 'email':
                    $utente = new Utente($db);
                    $data = json_decode(file_get_contents("php://input"));
                    $utente->email = $data->email;

                    if($utente->verifyEmail()){
                        $pwd_temp = $utente->createPasswordTemp();
                        $utente->update();

                        

                        send_email($utente,$pwd_temp);

                        //RESPONSE
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Email con password temporanea inviata.")
                        );
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Nessun utente registrato.")
                        );
                    }









                    break;
                default:
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Errore nella richieta.")
                    );
                    break;
            }
            break;
        case 'PUT':
            switch($action){
                case 'update':
                    $utente = new Utente($db);
                    $utenteOLD = new Utente($db);
                    $data = json_decode(file_get_contents("php://input"));
                    $utenteOLD->id = $data->id;
                    $utente->id = $data->id;
                    $utenteOLD->readOne();

                    if(!isset($data->email)){
                        $utente->email = $utenteOLD->email;
                    }else{
                        $utente->email = $data->email;
                    }
                    if(!isset($data->pwd)){
                        $utente->pwd = $utenteOLD->pwd;
                    }else{
                        $utente->createHashPassword($data->pwd);
                    }
                    if(!isset($data->nickname)){
                        $utente->nickname = $utenteOLD->nickname;
                    }else{
                        $utente->nickname = $data->nickname;
                    }
                    if(!isset($data->account_id)){
                        $utente->account_id = $utenteOLD->account_id;
                    }else{
                        $utente->account_id = $data->account_id;
                    }
                    
                    if($utente->update()){
                        http_response_code(200);
                        echo json_encode(
                            array("message" => "Aggiornamento completato.")
                        );
                    }else{
                        http_response_code(500);
                        echo json_encode(
                            array("message" => "Impossibile aggiornare l'utente.")
                        );
                    }
                    break;
                default:
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Errore nella richieta.")
                    );  
                    break;
            }
            break;
        case 'DELETE':
            $id = (int)$action; 
            if($id>0){
                // DELETE ONE ACCOUNT
                $utente = new Utente($db);
                $utente->id = $id;
                if($utente->delete()){
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Account cancellato.")
                    ); 
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Account NON cancellato.")
                    );  
                }
            }else if($id===0){
                // DELETE ALL ACCOUNT
                $utente = new Utente($db);
                if($utente->deleteUsers()){
                    http_response_code(200);
                    echo json_encode(
                        array("message" => "Gli account User sono stati cancellati.")
                    ); 
                }else{
                    http_response_code(500);
                    echo json_encode(
                        array("message" => "Gli account User NON sono stati cancellati.")
                    );  
                }
               
            }else{
                http_response_code(500);
                echo json_encode(
                    array("message" => "Errore nella richieta.")
                );  
            }
            break;
    }

?>