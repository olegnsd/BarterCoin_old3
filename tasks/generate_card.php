<?php
//ПРОВЕРИТЬ НА ОДНОЙ КАРТЕ!!!

require('../inc/init.php');

ini_set('display_errors', 0);

die();
if(!$_GET['cron']){
    die();
}
die();
//$myecho = json_encode($baseHref . "tasks/" . "1000_card" . ".csv");
//`echo " foptmp: "  $myecho >>/tmp/qaz`;

//`chmod 777 '1000_card.csv'`;
$foptmp = fopen("1000_card" . ".csv", "ab");

for($i=1; $i<=995; $i++){
    $cicl = 1;
    while($cicl){
        $number = '1000' . strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)) . strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)) . strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9)).strval(rand(0,9));
        //lhun create
        $number = luhn_create($number);

        $res = mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT id FROM accounts WHERE number = '$number'"));
        $res = $res['id'];
        //lhun test
        $luhn_test = luhn_test($number);

        if(!$res && $luhn_test){
            $cicl = 0;
        }
    }

    $expiremonth = '12';
    $expireyear = '23';

    $cicl = 1;
    while($cicl){      
        $cvc = strval(rand(1,9)) . strval(rand(0,9)) . strval(rand(0,9));
        if($cvc != '666'){
            $cicl = 0;
        }
    }

    $activated = '0';
    $balance = '0.00';
    $lim = '0';       //?
    $monthlim = '1500';//?
    $withdrawlim = '100';//?

    $name1 = "";//mysqli_escape_string($mysqli,$_POST['name1']);
    $name2 = "";//mysqli_escape_string($mysqli,$_POST['name2']);
    $name3 = "";//mysqli_escape_string($mysqli,$_POST['name3']);
    $phone_base = "";//'+' . mysqli_escape_string($mysqli,$_POST['phone']);

    $phone_utc = "";//phone_utc($phone_base); //узнать зону телефона

    $info_ip = "";//info_ip($ip_reg);
    $ip_reg = "";

    $query = "INSERT INTO `accounts` (number, expiremonth, expireyear, cvc, activated, balance, lim, monthlim, withdrawlim, name1, name2, name3, phone, phone_utc, ip_reg, info_ip) VALUES ('$number', '$expiremonth', '$expireyear', '$cvc', '$activated',  '$balance', '$lim', '$monthlim', '$withdrawlim', '$name1', '$name2', '$name3', '$phone_base', '$phone_utc', '$ip_reg', '$info_ip')";
   
//    $myecho = json_encode($query);
//    `echo " query_ins: "  $myecho >>/tmp/qaz`;
    
    $no_err = mysqli_query($mysqli, $query);
    
    fwrite($foptmp, ';' . $number . ';' . $expiremonth . "/" . $expireyear . ';' . $cvc . ';' . PHP_EOL);
    
    //$myecho = json_encode($send_out);
    `echo ';'$number";"$expiremonth"/"$expireyear";"$cvc";">>/home/bartercoin/tmp/qaz`;
    
//    $myecho = json_encode($card1);
//    `echo " card1: "  $myecho >>/tmp/qaz`;
//    $myecho = json_encode($card2);
//    `echo " card2: "  $myecho >>/tmp/qaz`;
 
// В случае карты с балансом
	 //$card1=getcard('1000506236751958');
    //$card2=getcard($number,$expiremonth,$expireyear,$cvc,0);
//    transaction($card1,$card2,'5', "Занесение 5 БР на новую вирт. карту ".$card2['number'], 0, $comission_act, $mincomission_act);
}

fclose($foptmp);
