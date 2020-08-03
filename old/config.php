<?
$mysql_conf['host']   = "localhost";
$mysql_conf['port']   = 3306;
$mysql_conf['user']   = "barter";
$mysql_conf['pass']   = "gq7hjSdKEI6SxsMO";
$mysql_conf['dbname'] = "barter";

//$mysql_conf['host']   = "db.h";
//$mysql_conf['port']   = 3306;
//$mysql_conf['user']   = "barter";
//$mysql_conf['pass']   = "f3c8g4EvsbrNF";
//$mysql_conf['dbname'] = "barter";

$mysqli=mysqli_connect($mysql_conf[host],$mysql_conf[user],$mysql_conf[pass],$mysql_conf['dbname']);
mysqli_set_charset($mysqli, "utf8");

$domain=$_SERVER['SERVER_NAME'];//domain.ltd
$folder="/";//начало и конец - "/"!
$baseHref="https://".$domain.$folder;//"https://".$domain.$folder;//my
$baseHrefLand="https://".$domain;
$servicename="BarterCoin";
$configLoaded=TRUE;

function phone($phone) {
$resPhone = preg_replace("/[^0-9]/", "", $phone);
if (substr($resPhone,0,1) == 8) $resPhone[0] = 7;
return $resPhone;
}
include('sms/ssms_su.php');
function sms($phone,$text){

$email = "bartercoin";
$password = "223mxzeq";
$r = smsapi_push_msg_nologin($email, $password, phone($phone), $text, array("sender_name"=>'EasySMS24'));
if($r[1])return true; else return false;
}
?>
