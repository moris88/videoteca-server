<?php
    include_once '../config/PHPMailer.php';
    include_once '../config/Exception.php';

function send_email($data, $elencoFilm){
    //print_r($data); //TEST
    
    $name = 'admin';
    $nickname = $data['nickname'];
    $email = $data['email'];
    $object = 'newsletter ultimi film inseriti';
    $message = 'Messaggio inviato da: '.$name.' a '.$nickname.'<br>';
    $message .= 'Email: '.$email.'<br>';
    $message .= 'Oggetto: '.$object.'<br>';
    $message .= 'Ultimi film aggiunti: <br>';
    foreach($elencoFilm as $key => $value){
        $message .= $key.' '.$value.'<br>';
        $message .= $key.' '.$value.'<br><hr>';
        $message .= $key.' '.$value.'<br>';
    }
    $message .= '---NON RISPONDERE A QUESTA EMAIL---<br>';

    try{
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        $mail->setFrom('newsletter@videoteca.it');
        $mail->addAddress($email);
        $mail->Subject = $object;
        $mail->msgHtml($message);
        $mail->send();
        return true;
    }catch(Exception $e){
        return false;
    }
}