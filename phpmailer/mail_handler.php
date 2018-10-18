<?php
require_once('email_config.php');
require('phpmailer/PHPMailer/src/PHPMailer.php');
require('phpmailer/PHPMailer/src/SMTP.php');
$message = [];
$output =[
    'success' => null,
    'message' => []
];

if($output['success'] !== null){
    http_response_code(422);
    echo json_encode($output);
    exit();
}

$message['name'] = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
if( empty($message['name'] )){
    $output['success'] = false;
    $output['message'][] = 'missing name key';
}

$message['email'] = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
if( empty($message['email'] )){
    $output['success'] = false;
    $output['message'][] = 'invalid  email key';
}

$message['message'] = filter_var($_POST['message'], FILTER_SANITIZE_STRING);
if( empty($message['message'] )){
    $output['success'] = false;
    $output['message'][] = 'missing  email key';
}

$message['subject']= filter_var($_POST['subject'], FILTER_SANITIZE_STRING);
if( empty($message['subject'] )){
    $output['success'] = false;
    $output['message'][] = 'missing  subject ';
}

 $message['phone']= preg_replace('/[^0-9]' , '' ,$_POST['phone_number']);
// if( ( empty($message['phone'] ) && (count($message['phone'])  >= 10 && (count($message['phone']) <= 11 ){
//     $output['success'] = false;
//     $output['message'][] = 'missing  phonenumber ';
// }

$mail = new PHPMailer\PHPMailer\PHPMailer;
$mail->SMTPDebug = 3;           // Enable verbose debug output. Change to 0 to disable debugging output.

$mail->isSMTP();                // Set mailer to use SMTP.
$mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers.
$mail->SMTPAuth = true;         // Enable SMTP authentication


$mail->Username = EMAIL_USER;   // SMTP username
$mail->Password = EMAIL_PASS;   // SMTP password
$mail->SMTPSecure = 'tls';      // Enable TLS encryption, `ssl` also accepted, but TLS is a newer more-secure encryption
$mail->Port = 587;              // TCP port to connect to
$options = array(
    'ssl' => array(
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    )
);
$mail->smtpConnect($options);
$mail->From = EMAIL_USER; //'example@gmail.com';  // sender's email address (shows in "From" field)
$mail->FromName = EMAIL_USERNAME;   // sender's name (shows in "From" field)
$mail->addAddress(EMAIL_TO_ADDRESS, EMAIL_USERNAME);  // Add a recipient
//$mail->addAddress('ellen@example.com');                        // Name is optional
$mail->addReplyTo ($message['email'], $message['name']);                          // Add a reply-to address

$message['subject'] = substr($message['message'],0, 78);
$mail->Subject = $message['subject'];

$mail->isHTML(true);                                  // Set email format to HTML
$message['message'] = nl2br($message['message']);   //convert newline characters
$mail->Body    =   $message['message'];  //'This is the HTML message body <b>in bold!</b>';
$mail->AltBody = htmlentities($message['message']);
//or
$mail->isHTML(false);
$mail->Body = $message['message'];

if(!$mail->send()) {
    $output['success'] = false;
    $output['message'][] = $mail->ErrorInfo;
    // echo 'Message could not be sent.';
    // echo 'Mailer Error: ' . $mail->ErrorInfo;
} else {
    $output['success'] = true;
    // echo 'Message has been sent';
}
echo json_encode($output);
?>
