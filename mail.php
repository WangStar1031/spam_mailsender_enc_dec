<?php

require 'mailer.php';

// $to = $argv[1];

$letterPath = 'scr.html';
$random = rand(100000000,999999999);
$url = rand(1,999);
$order = rand(100000000,999999999);
$docu = rand(100000000,999999999);
$letterPath = 'scr.html';
$arr = array("google.com");
$url = $arr[mt_rand(0, count($arr) - 1)];
$ordid = strtoupper(substr(str_shuffle(sha1(microtime())), 0, 12));
$ordid1 = strtoupper(substr(str_shuffle(sha1(microtime())), 0, 12));
$date = gmdate("j F Y");

if (!file_exists($letterPath)) {
        die('[!] Error: Letter file is missing!\n');
}

$currentDomain = gethostname();
$userName = get_current_user();
$message1 = file_get_contents($letterPath);
$message2 = str_replace("#LINK#", $url, $message1);
$message3 = str_replace("#TOKEN#", $random, $message2);
$message4 = str_replace("#ORDER#", $order, $message3);
$message5 = str_replace("#DOCU#", $docu ,$message4);
$message6 = str_replace("#ORDERID#", $ordid, $message5);
$message7 = str_replace("#DATE#", $date, $message6);

// if (!($fp = fopen("list.txt", "r")))
//     exit("Unable to open $listFile.");
$nCount=0;
date_default_timezone_set('UTC');
print "Start time is "; print date("Y:m:d H:i"); print "\n";
$strEmails = @file_get_contents("list.txt");
if( !$strEmails){
 exit("Unable to open $listFile.");
}
$arrEmails = explode("\n", $strEmails);
for( $i = 0; $i < count($arrEmails); $i++){
 $email = $arrEmails[$i];
 if( $email == "")
  continue;
 $scr = str_replace("#CLIENT#", $email, $message7);
 $mail = new PHPMailer;
 $mail->isSendmail();

$mail->setFrom($userName . '@' . $currentDomain, 'Sender');

$mail->MessageID = '<'.md5(time().getmypid()).'@'.$userName.'>';
$mail->Helo = $userName;
$mail->XMailer = "Mailer(8.171.7) $docu";

$mail->AddCustomHeader("Received", "from $userName (HELO $userName) ($userName@192.168.".rand(0,255).".".rand(0,255).") \n by $userName with 0 (qmail 1.03 + ejcp v14 + HB patch) with AES256-SHA encrypted SMTP; ".date("$date "));
$mail->AddCustomHeader("X-PP-REQUESTED-TIME:$order");
$mail->AddCustomHeader("X-PP-Email-transmission-Id:$random");
$mail->AddCustomHeader("X-MaxCode-Template::$ordid1");
$mail->AddCustomHeader("X-Email-Type-Id:::$ordid1");
$mail->AddCustomHeader("X-SID-PRA: SERVICE@PAYPAL.CO.UK");
$mail->AddCustomHeader("X-SID-Result: PASS");

 $mail->Subject = '=?UTF-8?B?'.base64_encode("Sender No.".rand(111111111,999999999)).'?=';
 $mail->Body = $scr;
 $mail->addAddress($email, '');
    try {
     $mail->send();
     $nCount++;
    } catch (Exception $e) {
     print_r($e);
    }
}
print "End time is "; print date("Y:m:d H:i"); print "\n";
print "$nCount"; print "emails sent."; print"\n";


?>
