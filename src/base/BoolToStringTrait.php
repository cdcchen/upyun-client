<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/3
 * Time: 13:06
 */

namespace cdcchen\upyun\base;


/**
 * Class BoolToStringTrait
 * @package cdcchen\upyun
 */
trait BoolToStringTrait
{
    /**
     * @param $value
     * @return string
     */
    public static function booleanToString($value)
    {
        return $value ? 'true' : 'false';
    }
}