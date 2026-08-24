<?php

if($_SERVER["REQUEST_METHOD"] == "POST"){

    $to = $_POST['email'];
    $message = $_POST['response'];

    $subject = "Response from RuCOPING";

    $headers = "From: RuCOPING <rucoping@gmail.com>\r\n";
    $headers .= "Reply-To: <rucoping@gmail.com>\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    if(mail($to, $subject, $message, $headers)){
        echo "Response sent successfully.";
    }else{
        echo "Email sending failed.";
    }
}

?>