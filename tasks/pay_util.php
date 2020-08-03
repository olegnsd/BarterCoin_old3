#!/usr/bin/php
<?php
//крон раз в час
ini_set('display_errors', 1);
//require('../inc/init.php');
require('../inc/config.php');
require('../inc/mysql.php');
require('../inc/functions.php');

`echo "       "   >>/home/bartercoin/tmp/qaz_pay_util`;
$myecho = date("d.m.Y H:i:s");
`echo " date_now : "  $myecho >>/home/bartercoin/tmp/qaz_pay_util`;

$pay_class = new pay_class;

//обновление данных о кол-ве запусков крона
mysqli_query($mysqli,"UPDATE `util` SET `num_launch`=(`num_launch`+1)");
$pay_class->sql_err($mysqli, 'update_util');

//проверка счета в банкомате-доноре
$info_d = mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT b.* FROM settings AS b, settings AS u WHERE u.title='util' AND u.amount=b.amount AND b.title='bankomat'"));
$pay_class->sql_err($mysqli, 'info_d');

$myecho = $info_d['amount'];//json_encode($info_d, JSON_UNESCAPED_UNICODE);
`echo " info_d_N: "  $myecho >>/home/bartercoin/tmp/qaz_pay_util`;

//выбор абонентов для проплаты
$pays = mysqli_query($mysqli,"SELECT * FROM `util` WHERE `pay` = '1' AND (`num_launch`+10)>=(`util_day`*24) HAVING DATEDIFF(CURRENT_TIMESTAMP(), `last_pay`) >= `util_day` AND CURRENT_TIME() >= `util_time` ORDER BY `prior` DESC");
$pay_class->sql_err($mysqli, 'pays');

//проплата жкх
$res_pay = true;
foreach($pays as $pay){
    if($res_pay){
        //несколько раз пытаться проверить счет в доноре
        $i = 3;
        while($i){
            $pay_class->amount_bank_d = $pay_class->check_balance($mysqli, $info_d);
            
            $echo = $pay_class->amount_bank_d;
            `echo "amount_bank_d: " $echo >>/home/bartercoin/tmp/qaz_pay_util`;
            
            if($pay_class->amount_bank_d == "err_am" && $pay_class->amount_bank_d != '0'){
                $i--;
            }else{
                $i = false;
            }
            sleep(60);
        }
        sleep(5);
    }
    if($pay_class->amount_bank_d == "err_am" && $pay_class->amount_bank_d != '0'){
        `echo "error_amount_bank_d_3" >>/home/bartercoin/tmp/qaz_pay_util`;
        die();
    }
    $res_pay = $pay_class->pay_util($mysqli, $pay, $info_d);
    sleep(5);
    //обновить счет донора
    //$pay_class->amount_bank_d = $pay_class->check_balance($mysqli, $info_d);
            
	//$echo = $pay_class->amount_bank_d;
	//`echo "amount_bank_d: " $echo >>/home/bartercoin/tmp/qaz_pay_util`;
            
    //file_put_contents('/home/bartercoin/tmp/bankbalance'.$info_d['amount'], $pay_class->amount_bank_d);
}
//обновить счет донора
$pay_class->amount_bank_d = $pay_class->check_balance($mysqli, $info_d);
		
$echo = $pay_class->amount_bank_d;
`echo "amount_bank_d: " $echo >>/home/bartercoin/tmp/qaz_pay_util`;
		
put_bank($mysqli, $info_d['amount'], $pay_class->amount_bank_d);//file_put_contents('/home/bartercoin/tmp/bankbalance'.$info_d['amount'], $pay_class->amount_bank_d);

class pay_class{
    public $amount_bank_d;
    
    public function __construct() { }
    
    //проплата ЖКХ
    public function pay_util($mysqli, $pay, $info_d){
        
        `echo "pay_util " >>/home/bartercoin/tmp/qaz_pay_util`;
        
        if($this->amount_bank_d == 'err_am' && $this->amount_bank_d != '0'){
            `echo "error_amount_bank_d " >>/home/bartercoin/tmp/qaz_pay_util`;
            return false;  
        }
        $this->amount_bank_d = floatval($this->amount_bank_d);
        if($this->amount_bank_d < $pay['util_value'] || $this->amount_bank_d < $pay['min_fiat']){
            `echo "tight_amount_bank_d " >>/home/bartercoin/tmp/qaz_pay_util`;
            return false; 
        }
        
        //определение суммы и предела пополнения 1-го
        $amount = $pay['util_value'] + rand(-2, 2);
        
        `echo "add amount: " $amount >>/home/bartercoin/tmp/qaz_pay_util`;
        
        //вызов функции проплаты
        $util_id_recip = $pay['util_id_recip'];//куда платить
        $util_id_prov = $pay['util_id_prov'];//провайдер киви
        $comment = "Оплата ЖКХ";
        $out = $this->pay_util_f($amount, $util_id_recip, $util_id_prov, $info_d, $comment);
        
        $myecho = $out;
        `echo " out_res_pay: "  $myecho >>/home/bartercoin/tmp/qaz_pay_util`;
        
        if (strpos($out,'Accepted')) {
            //обновление счета донора
            $out = json_encode($out, JSON_UNESCAPED_UNICODE);
            //обновление данных об оплате
            $update_util = mysqli_query($mysqli,"UPDATE `util` SET `last_pay`=CURRENT_TIMESTAMP, `num_launch`=0 WHERE `id`='".$pay['id']."'");
            $this->sql_err($mysqli, 'update_util');
            $insert_util = mysqli_query($mysqli,"INSERT INTO `util_log` ( `util_id`, `util_pay`, `answer`, `date`) VALUES ('".$pay['id']."', '$amount', '$out', CURRENT_TIMESTAMP)");
            $this->sql_err($mysqli, 'insert_util');
            
            return true;

        }
        
        `echo "pay_false " >>/home/bartercoin/tmp/qaz_pay_util`;
        
        return false;
    }
    
    //функция проплаты
    protected function pay_util_f($amount, $util_id_recip, $util_id_prov, $info_d, $comment){
        $token_proxy = json_decode($info_d["token"], true);
        $token = ($token_proxy["token"]);
        $proxy_ip = ($token_proxy["ip"]);
        $proxy_port = ($token_proxy["port"]);
        $proxy_usr = ($token_proxy["usr"]);
        $proxy_pass = ($token_proxy["pass"]);
        if( $curl = curl_init() ) {
            curl_setopt($curl, CURLOPT_URL, 'https://edge.qiwi.com/sinap/api/v2/terms/'.$util_id_prov.'/payments');
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            $id = 1000 * time();
            $json_data = '{"id":"' . $id . '","sum":{"amount":' . str_replace(',','.',(float)$amount) . ',"currency":"643"},"paymentMethod":{"type":"Account","accountId":"643"},"fields":{"account":"' . $util_id_recip . '"}}';
            
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

		$amount_bank = json_decode($amount_bank);
        $amount_bank = $amount_bank->accounts[0]->balance->amount;
        if(json_encode(curl_error($curl)) > 5){
            $amount_bank = 'err_am';
        }
        
        $title = $token_proxy["title"];
        $echo=json_encode($amount_bank, JSON_UNESCAPED_UNICODE);
        `echo "amount_bank $title: " $echo >>/home/bartercoin/tmp/qaz_add_br`;
        $echo=json_encode(curl_error($curl));
        `echo "amount_bank_err: " $echo >>/home/bartercoin/tmp/qaz_add_br`;
        
        curl_close($curl);

        return $amount_bank;
    }
    
    //вывод ошибок sql
    public function sql_err($mysqli, $fun){
        $myecho = json_encode(mysqli_error($mysqli), JSON_UNESCAPED_UNICODE);
        if(strlen($myecho) > 5)`echo " $fun : "  $myecho >>/home/bartercoin/tmp/qaz_pay_util`;
        return;
    }

}
