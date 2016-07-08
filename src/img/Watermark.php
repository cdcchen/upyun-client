<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/2
 * Time: 14:35
 */

namespace cdcchen\upyun\img;

use cdcchen\upyun\base\BoolToStringTrait;
use cdcchen\upyun\base\Object;
use cdcchen\upyun\base\ParamsTrait;


/**
 * Class Watermark
 * @package cdcchen\upyun
 */
abstract class Watermark extends Object
{
    use ParamsTrait, BoolToStringTrait, ImageMakerTrait;

    /**
     * Watermark constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    /**
     * init after construct
     */
    protected function init()
    {
    }

    /**
     * @return bool|string
     */
    public function buildParams()
    {
        if (!$this->beforeBuild()) {
            return false;
        }

        $params = $this->getParams();
        if (empty($params)) {
            return '';
        }

        $paramsStr = '/watermark';
        foreach ($params as $name => $value) {
            $paramsStr .= "/{$name}/{$value}";
        }

        return $paramsStr;
    }

    /**
     * @return bool
     */
    protected function beforeBuild()
    {
        return true;
    }

    /**
     * @param string $align
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setAlign($align)
    {
        if (!in_array($align, UrlImageMaker::aligns())) {
            throw new \InvalidArgumentException("Align: $align is not a valid value.");
        }
        return $this->setParam('align', $align);
    }

    /**
     * @param boolean $flag
     * @return $this
     */
    public function setAnimate($flag)
    {
        return $this->setParam('animate', static::booleanToString($flag));
    }

    /**
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function setMargin($x, $y)
    {
        if (!is_int($x) || !is_int($y)) {
            throw new \InvalidArgumentException('X and Y must be an integer');
        }
        return $this->setParam('margin', "{$x}x{$y}");
    }

    /**
     * @param int $opacity
     * @return $this
     */
    public function setOpacity($opacity)
    {
        if (!is_int($opacity) || $opacity < 0 || $opacity > 100) {
            throw new \InvalidArgumentException('Opacity must be an integer, and value between 0 and 100');
        }
        return $this->setParam('opacity', $opacity);
    }

    /**
     * @return bool|string
     */
    public function __toString()
    {
        return $this->buildParams();
    }
}