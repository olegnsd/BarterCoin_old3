<?php

require('../inc/init.php');

ini_set('display_errors', 1);

die();

$query = "DROP TABLE `time_zone`";
mysqli_query($mysqli, $query);

$path = "/home/bartercoin/web/bartercoin.holding.bz/public_html/";//"/var/www/bartercoin.holding.home/public_html/"
$folder = "tasks";
$file = "number_zone";

$content = file($path . $folder . "/" . $file . ".csv", FILE_IGNORE_NEW_LINES);

$query = "CREATE TABLE IF NOT EXISTS `time_zone` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone_cod` varchar(3),
  `phone_from` varchar(7),
  `phone_to` varchar(7),
  `zone` varchar(2),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=cp1251";
mysqli_query($mysqli, $query);

foreach ($content as $line) {
    $line_mass = explode(";", $line);
    
    $phone_cod = $line_mass[0];
    $phone_cod = preg_replace("/\D{1,}/", "", $phone_cod);
    
    $phone_from = $line_mass[1];
    $phone_from = preg_replace("/\D{1,}/", "", $phone_from);
    
    $phone_to = $line_mass[2];
    $phone_to = preg_replace("/\D{1,}/", "", $phone_to);
    
    $zone = $line_mass[3];
    $zone_mass = explode(":", $zone);
    $zone = $zone_mass[0];
    $zone = preg_replace("/\D{1,}/", "", $zone);
    $zone = ltrim($zone, "0");
//    echo $phone_from . " " . $phone_to . " " . $zone . "<br />\n";

    $query = "INSERT INTO `time_zone` (`phone_cod`, `phone_from`, `phone_to`, `zone`) VALUES
    ('$phone_cod', '$phone_from', '$phone_to', $zone)";

    mysqli_query($mysqli, $query);
}
