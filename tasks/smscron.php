<?php

ini_set('display_errors', 0);

$h_beg = 10;//время начала, часы
$h_end = 20;//время конца, часы
$d_day = 2;//промежуток отправки смс, дней

global $system_sms;

require('../inc/init.php');

require('../inc/functions_sms.php');


//убрать после заполнения базы
//$result=mysqli_query($mysqli,"SELECT id, ip_reg, phone  FROM `accounts` WHERE `ip_reg` IS NOT NULL AND `info_ip` IS NULL LIMIT 1");
    //if(!$result | !mysqli_num_rows($result)){
        //echo('<div class="alert">не найдены</div>');
    //}else{
        //$curl = curl_init();
        //$account=mysqli_fetch_assoc($result);
        
        //$phone_utc = phone_utc($account['phone'], $mysqli);

        //curl_setopt($curl, CURLOPT_URL, 'http://ru.sxgeo.city/json/'.$account['ip_reg']);//32b748942f69e9e841dc812be6b1e578
        //curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        //curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        //$out = json_decode(curl_exec($curl), true);

        //$out_fix = array('city' => $out['city']['name_ru'], 
                         //'region'  => array('name_ru'=>$out['region']['name_ru'], 'timezone'=>$out['region']['timezone'], 'utc'=>$out['region']['utc']),
                        //'country'  => array('name_ru'=>$out['country']['name_ru'], 'timezone'=>$out['country']['timezone'], 'utc'=>$out['country']['utc'])
                    //);
        //$info_ip = json_encode($out_fix, JSON_UNESCAPED_UNICODE);
        //$user_id = $account['id'];
        //$query = "UPDATE `accounts` SET `phone_utc`='$phone_utc', `info_ip`='$info_ip' WHERE `id`='$user_id'";
        //mysqli_query($mysqli, $query);        

        ////$myecho = json_encode(date("d.m.Y H-i-s"));
        ////`echo " date: "  $myecho >>/tmp/qaz`;

        //curl_close($curl);
    //}
    


$query = "SELECT * FROM `tasks_tasks_sms` WHERE `status`='0' ORDER BY `id` ASC";

$info_all=mysqli_query($mysqli, $query);

if($info_all->num_rows == 0)die();

while($info = mysqli_fetch_assoc($info_all)){

    $data=unserialize($info['data']);

    $tmp_card = $data['card'];
    if($tmp_card == 1){
        $tmp_card = "^1100.*";
    }elseif($tmp_card == 2){
        $tmp_card = "^1000.*";
    }else{
        $tmp_card = ".*"; 
    }

    $tmp_from = $data['from'];
    $tmp_to = $data['to'];
    $task_id = htmlspecialchars($info["id"]);
    $sms = htmlspecialchars($info["sms"]);
    $next = htmlspecialchars($info["next"]);
    $t_all = htmlspecialchars($info["t_all"]);
    $sim = htmlspecialchars($info["sim"]);
    if($sim == 1) $system_sms = 1;//системная
    if($sim == 2) $system_sms = 0;//рекламная

    
    $sql = "SELECT a.* FROM accounts AS a WHERE a.number REGEXP '$tmp_card' AND a.balance>='$tmp_from'  AND a.balance<='$tmp_to' AND a.activated='1' AND a.black='0' AND a.id <> ALL (SELECT user_id FROM task_$task_id) LIMIT 20";
    
    $users = mysqli_query($mysqli, $sql);
    
    if($users->num_rows == 0){
        $status_1 = mysqli_query($mysqli, "SELECT `status` FROM `task_$task_id` WHERE `status`='1'");
        if($status_1->num_rows > 0){
            $query = "UPDATE `tasks_tasks_sms` SET `status`='3' WHERE `id`='$task_id'";
        }else{
            $query = "UPDATE `tasks_tasks_sms` SET `status`='2', `t_all`='0' WHERE `id`='$task_id'";
        }
        mysqli_query($mysqli, $query);
        continue;
    }
    
    $loop = 20;
    while(($user = mysqli_fetch_array($users)) && $loop){
        //$cur_row = mysqli_fetch_assoc(mysqli_query($mysqli,"SELECT * FROM `task_$id_task` WHERE `id`='$next'"));
        $phone = $user['phone'];
        $phone = preg_replace("/^[8]/", '+7', $phone);
        $phone = preg_replace("/[^0-9]/", '', $phone);
        $phone=substr($phone,0,11);//echo($phone.'<hr>');
        $user_id = $user['id'];

        $ph_cod = substr($phone, 1, 3);
        $ph_numb = substr($phone, 4, 7);

        $sql = "SELECT `zone` FROM `time_zone` WHERE `phone_cod`='$ph_cod' AND `phone_from`<'$ph_numb' AND `phone_to`>'$ph_numb' LIMIT 1";
        $zone = mysqli_query($mysqli, $sql);
        if($zone->num_rows == 0){
            $zone = 3;
        }else{
            $zone = mysqli_fetch_array($zone);
            $zone = $zone['zone'];
        }
        
        $sql = "SELECT `last_sms` FROM `accounts` WHERE `id`='$user_id'";
        $last_sms = mysqli_fetch_array(mysqli_query($mysqli, $sql));
        $last_sms = $last_sms['last_sms'];

        $h_gm = date("H" ,mktime(gmdate("H")));//часы по гринвичу
        
        $h_curr = date("H" ,mktime(gmdate("H")+$zone));//часы по зоне mktime(gmdate("H")+3);

        if($h_curr < $h_beg || $h_curr >= $h_end || ($last_sms > (time() - 60 * 60 * 24 * $d_day))){
            $sql = "INSERT INTO `task_$task_id` (`user_id`, `phone`, `zone`, `status`) VALUES ('$user_id', '$phone', '$zone', '1')";
            
            mysqli_query($mysqli, $sql); 
            
            $user = false;
        }else{
            break 2;
        }
        $loop--;
    }
    
}


if(!isset($user) || !$user)die();

$CARDNUM = $user['number'];
$SURNAME = $user['name1'];
$NAME = $user['name2'];
$MIDDELNAME = $user['name3'];
$BALANCE = $user['balance'];
$CREDIT = $user['lim'];
$MONTHLIM = $user['monthlim'];
$WITHDRAWLIM = $user['withdrawlim'];
$number = $user['number'];
$expiremonth = $user['expiremonth'];
$expireyear = $user['expireyear'];
$cvc = $user['cvc'];
$number = str_split($number, 4);
$number = implode(" ", $number);
$expired = $expiremonth . "/" . $expireyear;
$name1_u = get_in_translate_to_en($SURNAME);
$name1_u = strtoupper($name1_u);
$name2_u = get_in_translate_to_en($NAME);
$name2_u = strtoupper($name2_u);
$holder = $name1_u . " " . $name2_u;

$data = array(
    'number' => $number,
    'expired' => $expired,
    'code' => $cvc,
    'holder' => $holder
);

$url_tmp = http_build_query($data);

$CARDLINK = $baseHref . "virt/scr.php?" . $url_tmp;

if(strlen($phone)==11){
    eval("\$sms = \"$sms\";");//подстановка переменных

    $send_out = send_sms_msg($phone, $sms);
    
    //$myecho = json_encode($send_out);
    //`echo " _smscron: "  $myecho >>/home/bartercoin/tmp/qaz`;
    
    $next++;
    $t_all--;
    mysqli_query($mysqli,"UPDATE `tasks_tasks_sms` SET `next`='$next', `t_all`='$t_all' WHERE `id`='$task_id'");
    
    $sql = "INSERT INTO task_$task_id (`user_id`, `phone`, `zone`, `status`) VALUES ('$user_id', '$phone', '$zone', '2')";
    mysqli_query($mysqli, $sql); 
    
    $sql = "UPDATE `accounts` SET `last_sms`=UNIX_TIMESTAMP(CURRENT_TIMESTAMP) WHERE `id`='$user_id'";
    mysqli_query($mysqli, $sql); 
    
}

