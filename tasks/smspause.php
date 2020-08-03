<?php

if(!$_GET['cron']){
    die();
}

require('../inc/init.php');

ini_set('display_errors', 0);

$h_beg = 10;//время начала, часы
$h_end = 20;//время конца, часы
$d_day = 2;//промежуток отправки смс, дней

$query = "SELECT * FROM `tasks_tasks_sms` WHERE `status`='3' ORDER BY `id` ASC";
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
    
    $h_gm = date("H" ,mktime(gmdate("H")));//часы по гринвичу

    $h_curr = date("H" ,mktime(date("H")));//часы по зоне
    
                                    //  20      10  0   5   12      15      23
    $base_beg = (int)($h_beg - $h_gm);//-10(14) 0   10  5   -2(22)  -5(19)  -13(11)
    $base_end = (int)($h_end - $h_gm);//0       10  20  15  8        5      -3(21)
    //0 1 2 3 4 5 6 7 8 9 10 11 12 13 14 15 16 17 18 19 20 21 22 23 
    if($base_beg < 0){
        $base_beg = (int)(24 + $base_beg);
    }
    if($base_end < 0){
        $base_end = (int)(24 + $base_end);
    }
    
    $d_time = time() - 60 * 60 * 24 * $d_day;
    
    if(abs($base_end - $base_beg) > ($h_end - $h_beg)){
        $query = "SELECT id FROM task_$task_id WHERE (zone>'$base_beg' OR zone<'$base_end') AND zone<>'false' AND zone<>'' AND status=1 AND user_id = ANY (SELECT id FROM accounts WHERE last_sms < '$d_time') LIMIT 1";
        $del_phone = mysqli_query($mysqli, $query);
        
        $query = "DELETE FROM task_$task_id WHERE (zone>'$base_beg' OR zone<'$base_end') AND zone<>'false' AND zone<>'' AND status=1  AND user_id = ANY (SELECT id FROM accounts WHERE last_sms < '$d_time')";
        //DELETE FROM task_234 WHERE zone>'23' AND zone<'9' AND zone<>'false' AND zone<>'' AND status=1
        
    }else{
        $query = "SELECT id FROM task_$task_id WHERE zone>'$base_beg' AND zone<'$base_end' AND zone<>'false' AND zone<>'' AND status=1 AND user_id = ANY (SELECT id FROM accounts WHERE last_sms < '$d_time') LIMIT 1";
        $del_phone = mysqli_query($mysqli, $query);
        
        $query = "DELETE FROM task_$task_id WHERE zone>'$base_beg' AND zone<'$base_end' AND zone<>'false' AND zone<>'' AND status=1 AND user_id = ANY (SELECT id FROM accounts WHERE last_sms < '$d_time')";
        //DELETE FROM task_234 WHERE zone>'0' AND zone<'10' AND zone<>'false' AND zone<>'' AND status=1
    }
    if(isset($del_phone) && $del_phone->num_rows > 0){
        mysqli_query($mysqli, $query);
    }
    
    if(isset($del_phone) && $del_phone->num_rows > 0){
        mysqli_query($mysqli,"UPDATE `tasks_tasks_sms` SET `status`='0' WHERE `id`='$task_id'");
    }else{
        $sql = "SELECT a.id FROM accounts AS a WHERE a.number REGEXP '$tmp_card' AND a.balance>='$tmp_from'  AND a.balance<='$tmp_to' AND a.activated='1' AND a.black='0' AND a.id <> ALL (SELECT user_id FROM task_$task_id) LIMIT 1";
        
        $users = mysqli_query($mysqli, $sql);
        
        if($users->num_rows > 0){
            mysqli_query($mysqli,"UPDATE `tasks_tasks_sms` SET `status`='0' WHERE `id`='$task_id'");
        }
    }
    
}
