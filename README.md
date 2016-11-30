# FizzMatch
a fund panel match lib (资金盘收款打款匹配算法), 改进了算法机制, 可以避免打款给自己, 避免匹配到自己

## installation (安装)
- 直接使用composer命令  
```
composer require fizzday/fizzmatch
```
- 或者写入composer.json
```
{
    "require": {
        "fizzday/fizzmatch": "dev-master"
    }
}
```
## usage samples (使用示例)
```
use Fizzday/FizzMatch/FizzMatch;

$payList[] = array('uid'=>1, 'id'=>11, 'money'=>200, 'param'=>1);
$payList[] = array('uid'=>2, 'id'=>12, 'money'=>400, 'param'=>2);
$payList[] = array('uid'=>3, 'id'=>13, 'money'=>200, 'param'=>3);

$getList[] = array('uid'=>1, 'id'=>21, 'money'=>100, 'param'=>1);
$getList[] = array('uid'=>5, 'id'=>22, 'money'=>500, 'param'=>2);
$getList[] = array('uid'=>6, 'id'=>23, 'money'=>200, 'param'=>3);

$adminList[] = array('uid'=>21, 'param'=>91);
$adminList[] = array('uid'=>21, 'param'=>92);
$adminList[] = array('uid'=>21, 'param'=>93);

$result = FizzMatch::setPayList($payList)->setGetList($getList)->setAdminList($adminList)->run();
```
match result (匹配结果)
```
$result = array(
    array("payid" => 11,"getid" => 22,"money" => 200,"payParam" => 1,"getParam" => 2,"payuid" => 1,"getuid" => 5),
    array("payid" => 12,"getid" => 22,"money" => 300,"payParam" => 2,"getParam" => 2,"payuid" => 2,"getuid" => 5),
    array("payid" => 12,"getid" => 23,"money" => 100,"payParam" => 2,"getParam" => 3,"payuid" => 2,"getuid" => 6),
    array("payid" => 13,"getid" => 23,"money" => 100,"payParam" => 3,"getParam" => 3,"payuid" => 3,"getuid" => 6),
    array("payid" => 13,"getid" => 0,"money" => 100,"payParam" => 3,"getParam" => 93,"payuid" => 3,"getuid" => 21)
)
```

## param comment (参数说明)
variable (变量)
- `$payList` 打款订单列表  
- `$getList` 收款订单列表  
- `$adminList` 系统中转账号列表  

variable field (变量字段)
```
uid : 用户 id
id : 将要匹配的订单 id
money : 将要匹配订单的金额
param : 额外参数参数, 可以是数组或字符串
```
return field (返回字段)
```
payid : 打款订单的 id
getid : 收款订单的 id
payuid : 打款人的 id
getuid : 收款人的 id
money : 匹配的金额
payParam : 打款的额外参数
getParam : 收款的额外参数
```
