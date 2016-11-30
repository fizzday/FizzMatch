<?php
/**
 * Created by PhpStorm.
 * User: fizzday
 * Date: 11/30 2016
 * Time: 14:07
 */

namespace Fizzday\FizzMatch;

/**
 * 自动匹配
 * 示例数据: 提现和收款人列表
 * $payList[] = array('uid'=>1, 'id'=>11, 'money'=>200, 'param'=>1);
 * $payList[] = array('uid'=>2, 'id'=>12, 'money'=>400, 'param'=>2);
 * $payList[] = array('uid'=>3, 'id'=>13, 'money'=>200, 'param'=>3);
 *
 * $getList[] = array('uid'=>1, 'id'=>21, 'money'=>100, 'param'=>1);
 * $getList[] = array('uid'=>5, 'id'=>22, 'money'=>500, 'param'=>2);
 * $getList[] = array('uid'=>6, 'id'=>23, 'money'=>200, 'param'=>3);
 *
 * $adminList[] = array('uid'=>21, 'param'=>91);
 * $adminList[] = array('uid'=>21, 'param'=>92);
 * $adminList[] = array('uid'=>21, 'param'=>93);
 * 示例数据: match返回
 * $return = array(
 * array("payid" => 11,"getid" => 22,"money" => 200,"payParam" => 1,"getParam" => 2,"payuid" => 1,"getuid" => 5),
 * array("payid" => 12,"getid" => 22,"money" => 300,"payParam" => 2,"getParam" => 2,"payuid" => 2,"getuid" => 5),
 * array("payid" => 12,"getid" => 23,"money" => 100,"payParam" => 2,"getParam" => 3,"payuid" => 2,"getuid" => 6),
 * array("payid" => 13,"getid" => 23,"money" => 100,"payParam" => 3,"getParam" => 3,"payuid" => 3,"getuid" => 6),
 * array("payid" => 13,"getid" => 0,"money" => 100,"payParam" => 3,"getParam" => 93,"payuid" => 3,"getuid" => 21)
 * )
 * @package App\MicroService
 */

class FizzMatch
{
    /**
     * 提供帮助列表
     * @var array
     */
    private static $payList = array();
    /**
     * 获得帮助列表
     * @var array
     */
    private static $getList = array();
    /**
     * 系统账号列表
     * @var array
     */
    private static $adminList = array();
    /**
     * 匹配结果列表
     * @var array
     */
    private static $matchList = array();

    /**
     * 设置提供帮助列表
     * @param array $payList
     * @return static
     */
    public static function setPayList($payList = array())
    {
        static::$payList = $payList;

        return new static();
    }

    /**
     * 设置获得帮助列表
     * @param array $getList
     * @return static
     */
    public static function setGetList($getList = array())
    {
        static::$getList = $getList;

        return new static();
    }

    /**
     * 设置系统账号列表
     * @param array $adminList
     * @return static
     */
    public static function setAdminList($adminList = array())
    {
        static::$adminList = $adminList;

        return new static();
    }

    /**
     * 执行匹配
     * @return array|bool
     */
    public static function run()
    {
        $payList   = self::$payList;
        $getList   = self::$getList;
        $adminList = self::$adminList;

        if (empty($payList) || empty($adminList)) return false;

        // 执行匹配, 并放入 匹配结果变量
        static::$matchList = self::matchCash($payList, $getList, $adminList);

        return static::$matchList;
    }

    /**
     * 匹配算法
     * @param $payList
     * @param $getList
     * @param $adminList
     * @return array
     */
    private static function matchCash($payList, $getList, $adminList)
    {
        // 将要匹配的打款金额
        static $payMoney = 0;
        // 将要匹配的收款金额
        static $getMoney = 0;
        // 将要匹配的打款订单id
        static $payid = 0;
        // 将要匹配的收款订单id
        static $getid = 0;
        // 匹配结果暂存区
        static $matchResult = array();

        if (empty($payList) || empty($adminList)) return $matchResult;


        // 匹配前, 先验证是否是把钱打给了自己
        if (!empty($getList)) {
            // 先验证收款人当前资金是否是分散的, 如果是分散的, 就过滤打款人为自己的列表, 如果是全新匹配, 就过滤收款人列表是自己的账号
            $delRepeatUid = self::delRepeatUid($payList, $getList, $payid, $getid);
            $payList      = $delRepeatUid['payList'];
            $getList      = $delRepeatUid['getList'];

            // 判断要匹配的剩下额度, 是否满足需求, 不能完全匹配, 就不匹配
            if ($getMoney == 0) {
                $moneyEnoughCheck = self::moneyEnoughCheck($payList, $getList, $payMoney, $getMoney);
                $payList          = $moneyEnoughCheck[0];
                $getList          = $moneyEnoughCheck[1];
            }
        }

        // 将操作的金额放入容器
        if (!$payMoney) $payMoney = isset($payList[0]['money']) ? $payList[0]['money'] : 0;
        if (!$getMoney) $getMoney = isset($getList[0]['money']) ? $getList[0]['money'] : 0;
        if (!$payid) $payid = isset($payList[0]['id']) ? $payList[0]['id'] : 0;
        if (!$getid) $getid = isset($getList[0]['id']) ? $getList[0]['id'] : 0;


        // 匹配结果, 先把公共附加信息放入数组, 便于返回
        $param             = array();
        $param['payParam'] = isset($payList[0]['param']) ? $payList[0]['param'] : '';
        $param['getParam'] = isset($getList[0]['param']) ? $getList[0]['param'] : '';
        $param['payuid']   = isset($payList[0]['uid']) ? $payList[0]['uid'] : '';
        $param['getuid']   = isset($getList[0]['uid']) ? $getList[0]['uid'] : '';

        // 判断收款人是否匹配完毕
        if (empty($getList[0])) { // 匹配完毕, 匹配系统账户
            $countAdmin        = count($adminList);    // 统计系统账号数量
            $adminIndex        = mt_rand(0, $countAdmin - 1); // 随机生成将要使用的账号索引
            $param['getParam'] = isset($adminList[$adminIndex]['param']) ? $adminList[$adminIndex]['param'] : '';   // 把公共附加信息放入数组, 便于返回
            $param['getuid']   = isset($adminList[$adminIndex]['uid']) ? $adminList[$adminIndex]['uid'] : '';   // 把公共附加信息放入数组, 便于返回

            $match         = array('payid' => $payList[0]['id'], 'getid' => 0, 'money' => $payMoney); // 开始匹配
            $matchResult[] = array_merge($match, $param);
            $payMoney      = 0;

            array_shift($payList);

            if (!empty($payList[0])) {
                self::matchCash($payList, $getList, $adminList);
            }

            return $matchResult;
        }

        // 计算匹配后剩余金额
        $minus = $payMoney - $getMoney;
        if ($minus > 0) {   // 打款的有剩余
            $money_real = $getMoney;    // 实际订单金额
            $match      = array('payid' => $payList[0]['id'], 'getid' => $getList[0]['id'], 'money' => $money_real);
            $payMoney   = $minus;         // 打款有剩余
            $getMoney   = 0;              // 收款重置为0
            array_shift($getList);
        } elseif ($minus < 0) { // 收款的有剩余
            $money_real = $payMoney;    // 实际订单金额
            $match      = array('payid' => $payList[0]['id'], 'getid' => $getList[0]['id'], 'money' => $money_real);
            $getMoney   = abs($minus);         // 收款有剩余
            $payMoney   = 0;              // 打款重置为0
            array_shift($payList);
        } else {
            $money_real = $payMoney;
            $match      = array('payid' => $payList[0]['id'], 'getid' => $getList[0]['id'], 'money' => $money_real);
            $getMoney   = 0;
            $payMoney   = 0;
            array_shift($payList);
            array_shift($getList);
        }
        $matchResult[] = array_merge($match, $param);

        if (isset($payList[0]) || ($payMoney > 0)) {
            self::matchCash($payList, $getList, $adminList);
        }

        return $matchResult;
    }

    /**
     * 取出将要匹配的为自己的uid
     * @param $payList
     * @param $getList
     * @param $payid
     * @param $getid
     * @return array
     */
    private static function delRepeatUid($payList, $getList, $payid, $getid)
    {
        // if (empty($payList)) return $matchResult;

        if ($getid > 0) {   // 收款资金分散
            if ($payList[0]['uid'] == $getList[0]['uid']) {
                array_shift($payList);
                if (!empty($paylist)) self::delRepeatUid($payList, $getList, $payid, $getid);
            }
        } elseif ($getid > 0) { // 打款资金分散
            if ($payList[0]['uid'] == $getList[0]['uid']) {
                array_shift($getList);
                if (!empty($getList)) self::delRepeatUid($payList, $getList, $payid, $getid);
            }
        } else { // 打款,收款刚好相对, 弹出收款
            if ($payList[0]['uid'] == $getList[0]['uid']) {
                array_shift($getList);
                if (!empty($getList)) self::delRepeatUid($payList, $getList, $payid, $getid);
            }
        }

        return array('payList' => $payList, 'getList' => $getList);
    }

    /**
     * 判断是否有足够的钱用于匹配
     * @param $payList
     * @param $getList
     * @param $payMoney
     * @param $getMoney
     * @return array
     */
    private static function moneyEnoughCheck($payList, $getList, $payMoney, $getMoney)
    {
        // 是否满足将要匹配的收款人的总额
        $outValue    = array('uid' => $getList[0]['uid']);
        $paySumMoney = self::sumFieldFromTwiceArray('money', $payList, $outValue); // 统计要打款的总额
        if ($payMoney == 0) $sum = $paySumMoney;
        else $sum = $paySumMoney + $payMoney - $payList[0]['money'];
        // echo $sum, '--'. $payMoney .'--'. $getMoney.'--'. $payList[0]['money'] .'--'. $getList[0]['money'] .PHP_EOL;
        // if ($getMoney == 0) {
        if ($sum < $getList[0]['money']) {
            array_shift($getList);
            if (!empty($getList)) self::moneyEnoughCheck($payList, $getList, $payMoney, $getMoney);
        }

        // }

        return array($payList, $getList);
    }

    /**
     * 计算二维数组指定 key 的和
     * @param $field          字段
     * @param $arr            二维数组
     * @param array $outValue 是否需要增加外部的数值
     * @return int
     */
    private static function sumFieldFromTwiceArray($field, $arr, $outValue = array())
    {
        $sum = 0;
        if (!empty($arr)) {
            $arr = json_decode(json_encode($arr), true);
            foreach ($arr as $k => $v) {
                if (!empty($v[$field])) {
                    if (!empty($outValue)) {
                        if ($outValue['uid'] != $v['uid']) $sum += $v[$field];
                    } else
                        $sum += $v[$field];
                }
            }
        }

        return $sum;
    }

}