<?php
/**
 * Created by PhpStorm.
 * User: fizz_long
 * Date: 11/9 0009
 * Time: 14:07
 */

namespace Fizzday\FizzMatch;

/**
 * 自动匹配
 * 示例数据: 提现和收款人列表
    $list = array(
        array( 'uid'=>1234, 'money'=>200 ),
        array( 'uid'=>1235, 'money'=>300 )
    );
 * 示例数据: match返回
    $match = array(
        array( 'payuid'=>1234 ,'getuid'=>1235, 'money'=>200),
        array( 'payuid'=>1234 ,'getuid'=>1236, 'money'=>300)
    );
 * Class MatchService
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
        $payList   = static::$payList;
        $getList   = static::$getList;
        $adminList = static::$adminList;

        if (empty($payList) || empty($adminList)) return false;

        // 执行匹配, 并放入 匹配结果变量
        static::$matchList = matchCash($payList, $getList, $adminList);

        return static::$matchList;
    }


}
