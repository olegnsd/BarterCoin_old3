#!/usr/bin/php
<?php

//КИВИ-БОТ или Резервное финансирование:
//4) Козырев Данис Дамирович - КИВИ-БОТ (ИНН: 732712124897)
//13G4 - +79295893547 :
//Киви - 9295893547 пароль g04qtFxKz6
//ЯД - danis.cozyrev@yandex.ru пароль R6MTHGtGrH 
//( номер кошелька 410017150193980 )
//делать систему материальной поддержки с рекапчей где 1 кошель можно раз 1 день получить 1р.

//6р подливает каждую минуту
//если 10 мин прошло и никто не снял - еще 6р
//и так до 50р подлвиать
//6р раз в 10 мин

ini_set('display_errors', 1);

//if (!$_GET['cron']) {
//    die();
//}

require('../inc/init.php');

ini_set('display_errors', 1);

//проверка базы логов
mysqli_query($mysqli, "DELETE FROM qaz_add_br WHERE data<(CURRENT_TIMESTAMP()-60*60*5)");

//`echo "       "   >>/home/bartercoin/tmp/qaz_add_br`;
//$myecho = date("d.m.Y H:i:s");
//`echo " date_now : "  $myecho >>/home/bartercoin/tmp/qaz_add_br`;

$add_class = new add_class;

//проверка счета в 7-м банкомате
$info_7=mysqli_query($mysqli,"SELECT * FROM settings WHERE title='bankomat' AND amount='7'");
$info_7=mysqli_fetch_assoc($info_7);
$amount_bank7 = $add_class->check_balance($mysqli, $info_7);
sleep(1);

//проверка счета в 5-м банкомате
$info_5=mysqli_query($mysqli,"SELECT * FROM settings WHERE title='bankomat' AND amount='5'");
$info_5=mysqli_fetch_assoc($info_5);
$amount_bank5 = $add_class->check_balance($mysqli, $info_5);
sleep(1);

//подлив из 5-го в 7-й
$add_class->add_5_7($mysqli, $amount_bank5, $amount_bank7, $info_5, $info_7);
sleep(1);

//проверка счета в 1-м банкомате
$info_1=mysqli_query($mysqli,"SELECT * FROM settings WHERE title='bankomat' AND amount='1'");
$info_1=mysqli_fetch_assoc($info_1);
$amount_bank1 = $add_class->check_balance($mysqli, $info_1);
sleep(1);

//подлив из 7-го в 1-й
$add_class->add_7_1($mysqli, $amount_bank1, $amount_bank7, $info_1, $info_7);

class add_class{
    public function __construct() { }
    
    //подлив с 5-го в 7-й
    public function add_5_7($mysqli, $amount_bank5, $amount_bank7, $info_5, $info_7){
        if(!$amount_bank7 || !$amount_bank5){
            $myecho = "error_amount_bank 7 5 ";
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            return false;  
        }
        $info_5_7 = mysqli_query($mysqli,"SELECT * FROM settings WHERE title='add_5_7'");
        if($info_5_7){
            $info_5_7 = mysqli_fetch_assoc($info_5_7);
            if(!$info_5_7['delta_time'])return false;//подливать в 7-й из 5-го
            $last_time = strtotime($info_5_7['last_time']);
            $last_d = date('d', $last_time);
            $cur_d = date('d');
            $cur_time = date('Hi',time());
            $time_add = json_decode($info_5_7['token'], true)['time_add'];
            $time_add = str_ireplace(':', '', $time_add);

            $myecho = " last_d  cur_d: " . json_encode($last_d .'  '.$cur_d);
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            $myecho = " time_add  cur_time: " . json_encode($time_add .'  '.$cur_time);
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            $myecho = " amount_bank7: " . json_encode($amount_bank7);
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");

            if(abs($cur_d - $last_d) > 0 && $cur_time > $time_add){
				
				$myecho = " last_d  cur_d: " .json_encode($last_d .'  '.$cur_d);
				mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
	            $myecho = " time_add  cur_time: ". json_encode($time_add .'  '.$cur_time);
	            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
	            $myecho = " amount_bank7: " . json_encode($amount_bank7);
	            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
				
                //обновление даты пополнения
                $res_5_7 = mysqli_query($mysqli,"UPDATE settings SET last_time=CURRENT_TIMESTAMP WHERE title='add_5_7'");
                if(!$res_5_7)return false;
                //определение суммы и предела пополнения из 5 в 7
                $balance=mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT * FROM balance12 WHERE `sum`>'". (int)$amount_bank5. "' ORDER BY `sum` ASC LIMIT 1"));
                $amount = $balance['add_sum'];
                $amount_max = $balance['max'];
                if($amount_bank7 >= $amount_max){
                    $myecho = "exit add_5_7 false ";
                    mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
                    return false;
                }

                //подлив из 5 в 7
#                $linkas_qiwi = '+79295841764';//7-й bankomat
                $linkas_qiwi = '+79859452651';//7-й bankomat
                $comment = "Подлив из 5 в 7 банкомат";
                $out = $this->add_br_f($amount, $info_5, $linkas_qiwi, $comment);

                if (strpos($out,'Accepted')) {
                    //обновление счета 7-го банкомата
                    sleep(2);
                    $out_count_7 = $this->check_balance($mysqli, $info_7);

                    $myecho="out_count_7: " . json_encode($out_count_7, JSON_UNESCAPED_UNICODE);
                    mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
                    $myecho="out_count_7_err: " . json_encode(curl_error($curl), JSON_UNESCAPED_UNICODE);
                    mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
                    $myecho = "out_count_7_bank: " . $out_count_7 ;
                    mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");

                    if($out_count_7){
                        put_bank($mysqli, 7, $out_count_7);//file_put_contents('/home/bartercoin/tmp/bankbalance7', $out_count_7);
                    }
                    return true;
                }
            }
            $myecho = "exit add_5_7 false ";
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
        }
        return false;
    }

    //подлив из 7 в 1
    public function add_7_1($mysqli, $amount_bank1, $amount_bank7, $info_1, $info_7){
        if(!$amount_bank7){//(!$amount_bank1 || !$amount_bank7)
            $myecho = "error_amount_bank 7 1 ";
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            return false;  
        }
        $amount_bank1 = floatval($amount_bank1);

        //инфа подлива из 7 в 1
        $info=mysqli_query($mysqli,"SELECT * FROM settings WHERE title='add_br'");
        $info=mysqli_fetch_assoc($info);
        $delta_time = $info["delta_time"];
        $last_time = $info["last_time"];
        $last_time = strtotime($last_time);
        //определение суммы и предела пополнения 1-го
        $balance=mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT * FROM balance WHERE `sum`>'". (int)$amount_bank7. "' ORDER BY `sum` ASC LIMIT 1"));
        $amount = $balance['add_sum'];
        $amount_max = $balance['max'];

        $myecho = "amount_bank7, amount_bank, amount, amount_max: " . $amount_bank7 .", ". $amount_bank1 .", ". $amount. ", " .$amount_max;
        mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");

        if($amount_bank1 >= $amount_max && $amount_bank1 > 1.03){
            $myecho = "exit add_7_1 false ";
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            return false;
        }

        if($last_time > (time() - 60 * ($delta_time-1))){
            $myecho = "exit add_7_1 time ";
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
            return false;
        }
        
        $myecho = "add ";
        mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
        
        //подлив из 7 в 1
#        $linkas_qiwi = '+79295841764';//'+79295842531';//1-й bankomat
#        $linkas_qiwi = '+79859452651';//'+79295842531';//1-й bankomat
        $linkas_qiwi = '+79154197372';//'+79295842531';//1-й bankomat

        $comment = "Подлив из 7 в 1 банкомат";
        $out = $this->add_br_f($amount, $info_7, $linkas_qiwi, $comment);

        if (strpos($out,'Accepted')) {
            //обновление счета 1-го
            $out_count = $this->check_balance($mysqli, $info_1);

            $myecho = "out_count_1_bank: " . $out_count;
            mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");

            if($out_count){
                put_bank($mysqli, 1, $out_count);//file_put_contents('/home/bartercoin/tmp/bankbalance1', $out_count);
            }
            //обновление счета 7-го
            put_bank($mysqli, 7, $out_count);//file_put_contents('/home/bartercoin/tmp/bankbalance7', $amount_bank7 - $amount);

            mysqli_query($mysqli,"UPDATE settings SET last_time=CURRENT_TIMESTAMP WHERE title='add_br'");
            
            return true;

        }
        return false;
    }

    //проверка счета в банкомате
    public function check_balance($mysqli, $info){
        $token_proxy = json_decode($info["token"], true);
        $token = ($token_proxy["token"]);
        $proxy_ip = ($token_proxy["ip"]);
        $proxy_port = ($token_proxy["port"]);
        $proxy_usr = ($token_proxy["usr"]);
        $proxy_pass = ($token_proxy["pass"]);
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://edge.qiwi.com/funding-sources/v1/accounts/current');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);   
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Accept: application/json',
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token) //$qiwi_token
            ); 
            curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_usr.':'.$proxy_pass);

            $amount_bank = curl_exec($curl); //my
        }

        $myecho="amount_bank $token: " . json_encode($amount_bank, JSON_UNESCAPED_UNICODE);
        mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
        $myecho="amount_bank_err: " . json_encode(curl_error($curl), JSON_UNESCAPED_UNICODE);
        mysqli_query($mysqli, "INSERT INTO qaz_add_br (event) values('$myecho')");
        
        curl_close($curl);

        $amount_bank = json_decode($amount_bank);
        $amount_bank = $amount_bank->accounts[0]->balance->amount;

        return $amount_bank;
    }

    //функция подлива
    protected function add_br_f($amount, $info, $linkas_qiwi, $comment){
        $token_proxy = json_decode($info["token"], true);
        $token = ($token_proxy["token"]);
        $proxy_ip = ($token_proxy["ip"]);
        $proxy_port = ($token_proxy["port"]);
        $proxy_usr = ($token_proxy["usr"]);
        $proxy_pass = ($token_proxy["pass"]);
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://edge.qiwi.com/sinap/api/v2/terms/99/payments');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            $id = 1000 * time();
            $json_data = '{"id":"' . $id . '","sum":{"amount":' . str_replace(',','.',(float)$amount) . ',"currency":"643"},"paymentMethod":{"type":"Account","accountId":"643"}, "comment":"'.$comment.'","fields":{"account":"' . $linkas_qiwi . '"}}';
            curl_setopt($curl, CURLOPT_POSTFIELDS, $json_data);    			
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($json_data),
                'Authorization: Bearer ' . $token)
            ); 
            curl_setopt($curl, CURLOPT_PROXY, $proxy_ip);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy_port);
            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
            curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_BASIC);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_usr.':'.$proxy_pass);

            $out = curl_exec($curl); //my
            curl_close($curl);

            return $out;
        }
        return false;
    }

}

