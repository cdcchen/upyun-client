<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/3
 * Time: 13:09
 */

namespace cdcchen\upyun\img;


/**
 * Class ImageMakerTrait
 * @package cdcchen\upyun
 */
trait ImageMakerTrait
{
    public static function rangeValidate($name, $value, $start, $end)
    {
        if ($start < $start || $value > $end) {
            throw new \InvalidArgumentException("{$name}: {$value} is not between {$start} and {$end}.");
        }
    }

    /**
     * @param string $value
     * @return bool
     */
    protected static function validateColor($value)
    {
        return (bool)preg_match('/[\da-fA-F]{6}/i', $value);
    }

    /**
     * @param string $value
     * @return int
     */
    protected static function validateAlphaColor($value)
    {
        return (bool)preg_match('/[\da-fA-F]{8}/i', $value);
    }
}