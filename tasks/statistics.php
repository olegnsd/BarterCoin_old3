
<?php

ini_set('display_errors', 0);

if(!isset($_POST['stat']) || $_POST['stat'] != 'kj54n9gub9249'){
    die();
}

require('../inc/init.php');

require('../inc/functions_sms.php');

$query = "SELECT COUNT(id) FROM `accounts` WHERE `activated`='1'";

//$myecho = json_encode($query);
//`echo " query_task: "  $myecho >>/tmp/qaz`;

$barter_activ = mysqli_query($mysqli, $query);
$barter_activ = mysqli_fetch_assoc($barter_activ);
$barter_activ = $barter_activ['COUNT(id)'];

$stat_barter = array(
	'barter_activ' => $barter_activ
	);
echo(json_encode($stat_barter));

