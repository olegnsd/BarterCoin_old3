<?php
global $mysqli;

if($_POST['secret'] != 'erov74rvue')die("secret false");

require('../inc/init.php');

if($_POST['card']){
    $card = getcard($_POST['card']);

    if($card['id'] == 0 || $card['black'] == 1)die("false card");

    echo("OK");
}

if($_POST['sum']){
    $card = getcard($_POST['number']);

    echo($card['balance'] + $card['lim']);
}

