<?php
mb_internal_encoding("UTF-8");

// smtp mode is false, local sendmail.
$SMTP_MODE = true;

require_once('conf.php');

if( !isset($smpt) ) $SMTP_MODE = $smpt;

if( !isset($FromAddr) ) $FromAddr = 'from@example.com';
if( !isset($FromName) ) $FromName = '日本語送信者';
if( !isset($TO)       ) $TO       = 'address@example.com';
if( !isset($TO_Name)  ) $TO_Name  = '宛先日本語名';
if( !isset($subject)  ) $subject  = 'メールのテスト（サブジェクト）';
if( !isset($body)     ) $body     = "メール本文の内容。";

list( $user, $domain ) = split('@', $TO, 2);

if (getmxrr ( $domain , &$mxhosts  ) == False) {
	$MXHOST="localhost";
}
echo "mxhost = $mxhosts[0]\n";


require_once('qdsmtp.php');
require_once('qdmail.php');
$mail = & new Qdmail();

if( $SMTP_MODE ) {

	$mail -> smtp(true);
	$param = array(
		"host" => $mxhosts[0],
		"port" => 25,
		"from" => $FromAddr,
		"protocol" => "SMTP"
	);
	$mail -> smtpServer($param);

}

$mail -> debug(1);
$mail -> errorlogLevel(1);
$mail -> logLevel(1);
$mail -> errorDisplay( true );
// $mail -> bodyEmptyAllow( true );
$mail -> smtpObject()->error_display = true;
$mail -> smtpLoglevelLink( true );

$to = array($TO , $TO_Name);
$from = array($FromAddr , $FromName);

$mail -> to($to);
$mail -> subject($subject);
$mail -> from($from);
$mail -> text($body);
$return_flag = $mail -> send();

//var_dump($return_flag);

if(! $return_flag ){
	echo "Qdmail Error\n";
	print_r($mail -> errorStatment());
	echo "QdSMTP Error Stack\n";
	print_r($mail -> smtpObject()-> error_stack);
	echo "QdSMTP Error\n";
	print_r($mail -> smtpObject()-> error);
	exit(1);
}
exit(0);

?>
