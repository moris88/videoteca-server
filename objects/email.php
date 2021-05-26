<?php
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;
    use PHPMailer\PHPMailer\SMTP;

    require '../config/Exception.php';
    require '../config/PHPMailer.php';
    require '../config/SMTP.php';

function send_email($data, $elencoFilm){
    //print_r($data); //TEST
    $name = 'admin';
    $nickname = $data['nickname'];
    $toEmail = $data['email'];
    $mail = new PHPMailer(true);
    try {
        //Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'videoteca.app@gmail.com';                     //SMTP username
        $mail->Password   = 'Cecilia1992@';                               //SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
        $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
    
        //Recipients
        $mail->setFrom('videoteca.app@gmail.com', $name);
        $mail->addAddress($toEmail);               //Name is optional
    
        //Content
        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = 'newsletter ultimi film inseriti';
        $mail->Body = '
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Email</title>
        </head>
        <body> 
            <p>Messaggio inviato da: '.$name.' a '.$nickname.'</p>
        ';
        $mail->Body .= '<h1>Ultimi 3 film aggiunti: </h1>';
        foreach($elencoFilm as $films => $film){
            foreach($film as $key => $value){
                $mail->Body .= "<p>TITOLO: ".$value['titolo'].'</p>';
                $mail->Body .= "<p>GENERE: ".$value['genere'].'</p>';
                $mail->Body .= "<p>DURATA: ".$value['durata'].'</p>';
                //$mail->Body .= "<p>TRAMA: <br>".$value['trama'].'</p>';
                $mail->Body .= '<br><hr>';
            }
        }
        $mail->Body .= '<a href="http://192.168.1.208">Vai al sito web</a><br>';
        $mail->Body .= '---NON RISPONDERE A QUESTA EMAIL---<br>';
        $mail->Body .= '</body></html>';

        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
    
        $mail->send();
        return true;
    } catch (Exception $e) {
        //echo $mail->ErrorInfo;
        return false;
    }
}