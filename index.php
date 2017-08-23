<?php
$logFile = __DIR__ . '/' . date('Y-m-d').'.txt';
$file = file_get_contents('ip.txt');
$ipBlackList = explode("\n", $file);

$time = date("Y-m-d H:i:s");
$ip = get_client_ip();

$cloak = False;
foreach($ipBlackList as $value){
	$value = trim($value);
	if ($value == '' || substr($value,0,1) == '#'){
		continue;
	}
	if(ip_in_range( $ip, $value )){
		$cloak = True;
	}
  }
if (isset($_SERVER['HTTP_USER_AGENT'])){
	if (stripos($_SERVER['HTTP_USER_AGENT'], 'google') !== false){
		$cloak = True;
	}

	if (stripos($_SERVER['HTTP_USER_AGENT'], 'bot') !== false){
		$cloak = True;
	}
}

$log = $time.chr(9);
$log .= $ip.chr(9);
$log .= $cloak?'Cloaked':'Non-Cloak';
file_put_contents($logFile, $log.chr(10), FILE_APPEND);

if($cloak){
	include 'index.html';
}else{
	include 'lp.php';
}

function ip_in_range( $ip, $range ) {
	if ($ip == false){
		return false;
	};
	if ( strpos( $range, '/' ) == false ) {
		$range .= '/32';
	}
	list( $range, $netmask ) = explode( '/', $range, 2 );
	$range_decimal = ip2long( $range );
	$ip_decimal = ip2long( $ip );
	$wildcard_decimal = pow( 2, ( 32 - $netmask ) ) - 1;
	$netmask_decimal = ~ $wildcard_decimal;
	return ( ( $ip_decimal & $netmask_decimal ) == ( $range_decimal & $netmask_decimal ) );
}

function get_client_ip() {
    $ipaddress = '';
	if (isset($_SERVER["HTTP_CF_CONNECTING_IP"]))
		$ipaddress = $_SERVER["HTTP_CF_CONNECTING_IP"];
    else if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = '8.8.8.8';
    return $ipaddress;
}