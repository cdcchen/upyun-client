<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 14:02
 */

namespace cdcchen\upyun\av;


use cdcchen\upyun\base\Object;
use cdcchen\upyun\base\ParamsTrait;

/**
 * Class FetchFileTask
 * @package cdcchen\upyun\av
 */
class FetchFileTask extends Object
{
    use ParamsTrait;

    /**
     * FetchFileTask constructor.
     */
    public function __construct()
    {
        $this->setRandom(false)->setOverwrite(true);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setUrl($value)
    {
        return $this->setParam('url', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSaveAs($value)
    {
        return $this->setParam('save_as', $value);
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function setOverwrite($value)
    {
        return $this->setParam('overwrite', (bool)$value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setRandom($value)
    {
        return $this->setParam('random', (bool)$value);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getParams();
    }
}