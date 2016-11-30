<?php
// after command  'composer install'

require __DIR__."/../vendor/autoload.php";

use Fizzday\FizzMatch\FizzMatch;


$payList[] = array('uid'=>1, 'id'=>11, 'money'=>200, 'param'=>1);
$payList[] = array('uid'=>2, 'id'=>12, 'money'=>400, 'param'=>2);
$payList[] = array('uid'=>3, 'id'=>13, 'money'=>200, 'param'=>3);

$getList[] = array('uid'=>1, 'id'=>21, 'money'=>100, 'param'=>1);
$getList[] = array('uid'=>5, 'id'=>22, 'money'=>500, 'param'=>2);
$getList[] = array('uid'=>6, 'id'=>23, 'money'=>200, 'param'=>3);

$adminList[] = array('uid'=>21, 'param'=>91);
$adminList[] = array('uid'=>21, 'param'=>92);
$adminList[] = array('uid'=>21, 'param'=>93);

// $getList = array();
$result = MatchService::setPayList($payList)->setGetList($getList)->setAdminList($adminList)->run();

print_r($a);

// result

//$result = array(
//    array("payid" => 11,"getid" => 22,"money" => 200,"payParam" => 1,"getParam" => 2,"payuid" => 1,"getuid" => 5),
//    array("payid" => 12,"getid" => 22,"money" => 300,"payParam" => 2,"getParam" => 2,"payuid" => 2,"getuid" => 5),
//    array("payid" => 12,"getid" => 23,"money" => 100,"payParam" => 2,"getParam" => 3,"payuid" => 2,"getuid" => 6),
//    array("payid" => 13,"getid" => 23,"money" => 100,"payParam" => 3,"getParam" => 3,"payuid" => 3,"getuid" => 6),
//    array("payid" => 13,"getid" => 0,"money" => 100,"payParam" => 3,"getParam" => 93,"payuid" => 3,"getuid" => 21)
//);