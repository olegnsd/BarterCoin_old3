<?php

//echo('begin');
//echo('<br>');

$cron = $argv[1];//'cron6538';

if($cron != 'cron6538'){
    die();
}

// 
require('../inc/init.php');

//ini_set('display_errors', 1);

$fromnum =  $argv[2];//'1000300701615314';
$tonum =  $argv[3];//'1000408733095056';
$sum =  $argv[4];//'11';

$card1=getcard($fromnum);
$card_2=getcard($tonum);

//$myecho = json_encode($argv[2]);
//`echo " argv: "  $myecho >>/var/www/tmp/qaz`;
//$myecho = json_encode($card1);
//`echo " card1: "  $myecho >>/var/www/tmp/qaz`;
//exit;
	
	global $mysqli;
	$sql = mysqli_query($mysqli, "SELECT * FROM tasks_user_api_calls");//.";WHERE user_id = ".$current_user_id; //1985-10-03
    $res = mysqli_fetch_assoc($sql);
	
	if($res['mode'] == 1){
		chmod('/home/bartercoin/tmp/csv/synthesized-audio' . '.csv', 0777);
		$foptmp = fopen('/home/bartercoin/tmp/csv/synthesized-audio' . '.csv', "w");
		//
		for($i=0; $i<=2; $i++){
			$phone = "7" . strval(mt_rand(1000000000, 9999999999));
			fwrite($foptmp, ';' . $res['prefix'] . $phone . ';' . PHP_EOL);
		}
		$user_phone = clear_phone($card_2['phone']);//$row['user_phone'];
		fwrite($foptmp, ';' . $res['prefix'] . $user_phone . ';' . PHP_EOL);

		$user1 = $card1['name1'] . " " . $card1['name2'] . " ". $card1['name3'];//sms
		$sum1 = $sum;//сумма перевода sms
		$balance2 = number_format($card_2['balance'], 2, ',', ' ');//$row['sum'];//баланс получателя sms
		$card2 = '*'.substr($card_2['number'],-4);//карта получателя sms
		$card_don = $card1['number'];
		$card_rec = $card_2['number'];
		fclose($foptmp);

        $client_id = strval(mt_rand(1, 320));

		if(!$curl = curl_init()){
		   die(); 
		}
		$comment = "Перевод ". $sum1 ." BCR c " . $card_don . " на ". $card_rec;
		$sms_text = $res['sms_text'];
		eval("\$sms_text = \"$sms_text\";");//подстановка переменных
		$cfile_wav = new CURLFile('/home/bartercoin/tmp/audio/'.$res['file_name'],'audio/x-wav','10wav');
		$cfile_csv = new CURLFile('/home/bartercoin/tmp/csv/synthesized-audio' . '.csv','mybase1');
		
		//echo('cfile_wav: '. '/home/bartercoin/tmp/audio/'.$res['file_name'] );
		//echo('<br>');
		//echo('cfile_csv: '. '/home/bartercoin/tmp/csv/synthesized-audio' . '.csv' );
		//echo('<br>');
		//echo('cfile_wav: '. json_encode($cfile_wav));
		//echo('<br>');
		//echo('cfile_csv: '. json_encode($cfile_csv));
		//echo('<br>');
		
		$query = array(
			'comment' => $comment,
			'caller' => $res['caller'],
			'client_id' => $client_id,
			'timefrom' => $res['timefrom'],
			'timeto' => $res['timeto'],
			'prior' => $res['prior'],
			'sleep' => '75',
			'typebase' => 'file',
			'sms_enable' => $res['sms_enable'],
			'sms_text' => $sms_text,
			'email_enable' => '0',
			'email_text' => '',
			'file' => $cfile_csv,
			'range1' => '9260000000',
			'range2' => '9269999999',
			'sound' => $cfile_wav,
			'email_notify' => '',
			'url_notify' => '',
		);

		curl_setopt($curl, CURLOPT_URL, 'https://call.holding.bz/task/save/'.$res['api_key']);//d2b183ac4d8edb6f5a206bb1f34079eb
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $query); 
		curl_setopt($curl, CURLOPT_ENCODING, '');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: multipart/form-data',
		));

		$out = curl_exec($curl);

		if(curl_errno($curl)){
			$msg = curl_error($curl);
		}else{
			$msg = "File upload successfully";
		}
		
		//echo('out: '. json_encode($out));
		//echo('<br>');
		//echo('curl_error: '. json_encode(curl_error($curl)));
		//echo('<br>');

		curl_close($curl);
		
		//echo('end');

	}
    
?>
