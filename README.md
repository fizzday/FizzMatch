# FizzMatch
a fund panel match lib (资金盘收款打款匹配算法)

## usage
- 直接使用composer命令  
```
composer require fizzday/fizzmatch
```
- 写入composer.json
```
{
    "require": {
        "fizzday/fizzmatch": "dev-master"
    }
}
```
## samples
```
use Fizzday/FizzMatch/FizzMatch;

$payList[] = array('uid'=>11, 'money'=>200);
$payList[] = array('uid'=>12, 'money'=>200);
$payList[] = array('uid'=>13, 'money'=>500);

$getList[] = array('uid'=>21, 'money'=>100);
$getList[] = array('uid'=>22, 'money'=>200);
$getList[] = array('uid'=>23, 'money'=>200);

$adminList[] = array('uid'=>31);
$adminList[] = array('uid'=>32);
$adminList[] = array('uid'=>33);

// $getList = array();
$a = MatchService::setPayList($payList)->setGetList($getList)->setAdminList($adminList)->run();

vd($a);

```
result
```
//Array
//(
//    [0] => Array
//    (
//        [payuid] => 11
//            [getuid] => 21
//            [money] => 100
//        )
//
//    [1] => Array
//(
//    [payuid] => 11
//            [getuid] => 22
//            [money] => 100
//        )
//
//    [2] => Array
//(
//    [payuid] => 12
//            [getuid] => 22
//            [money] => 100
//        )
//
//    [3] => Array
//(
//    [payuid] => 12
//            [getuid] => 23
//            [money] => 100
//        )
//
//    [4] => Array
//(
//    [payuid] => 13
//            [getuid] => 23
//            [money] => 100
//        )
//
//    [5] => Array
//(
//    [payuid] => 13
//            [getuid] => 31
//            [money] => 400
//        )
//
//)
```
