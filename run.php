<?php
$option = "";
if( isset($argv[1])){
	$option = $argv[1];
}
$password = "password";
if( isset($argv[2])){
	$password = $argv[2];
}

function encrypt($string, $key){
	$iv = mcrypt_create_iv(
	    mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC),
	    MCRYPT_DEV_URANDOM
	);

	$encrypted = base64_encode(
	    $iv .
	    mcrypt_encrypt(
	        MCRYPT_RIJNDAEL_128,
	        hash('sha256', $key, true),
	        $string,
	        MCRYPT_MODE_CBC,
	        $iv
	    )
	);
	return $encrypted;
}
function decrypt($encrypted, $key){
	$data = base64_decode($encrypted);
	$iv = substr($data, 0, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC));

	$decrypted = rtrim(
	    mcrypt_decrypt(
	        MCRYPT_RIJNDAEL_128,
	        hash('sha256', $key, true),
	        substr($data, mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC)),
	        MCRYPT_MODE_CBC,
	        $iv
	    ),
	    "\0"
	);
	return $decrypted;
}
if( $option == "enc"){
	echo "encrypting ...\n";
	$mail_content = @file_get_contents("mail.php");
	$mailer_content = @file_get_contents("mailer.php");
	if( !$mail_content || !$mailer_content){
		exit("No source files.");
	}
	$enc_mail = encrypt($mail_content, $password);
	$enc_mailer = encrypt($mailer_content, $password);
	file_put_contents("mail.enc", $enc_mail);
	file_put_contents("mailer.enc", $enc_mailer);
	unlink("mail.php");
	unlink("mailer.php");
	exit("Encrypt done.");
}
if( $option == "dec"){
	echo "decrypting ...\n";
	$mail_content = @file_get_contents("mail.enc");
	$mailer_content = @file_get_contents("mailer.enc");
	if( !$mail_content || !$mailer_content){
		exit("No encryption files.");
	}
	$dec_mail = decrypt($mail_content, $password);
	$dec_mailer = decrypt($mailer_content, $password);
	file_put_contents("mail.php", $dec_mail);
	file_put_contents("mailer.php", $dec_mailer);
	unlink("mail.enc");
	unlink("mailer.enc");
	require_once "mail.php";
}
?>