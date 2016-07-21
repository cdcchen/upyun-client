<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/20
 * Time: 20:25
 */

namespace cdcchen\upyun\av;


use cdcchen\upyun\base\BoolToStringTrait;
use cdcchen\upyun\base\Object;
use cdcchen\upyun\base\ParamsTrait;

class AVTask extends Object
{
    use ParamsTrait, BoolToStringTrait;

    const TYPE_VIDEO     = 'video';
    const TYPE_HLS       = 'hls';
    const TYPE_THUMBNAIL = 'thumbnail';
    const TYPE_AUDIO     = 'audio';
    const TYPE_PROBE     = 'probe';

    /**
     * @param string $type
     * FetchFileTask constructor.
     */
    public function __construct($type)
    {
        if ($type) {
            $this->setType($type);
        }

        $this->setDefaultValues()->init();
    }

    public function init()
    {
    }

    protected function setDefaultValues()
    {
        $this->setReturnInfo(false);
        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        return $this->setParam('type', $type);
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
     * @param bool $flag
     * @return $this
     */
    public function setReturnInfo($flag = true)
    {
        return $this->setParam('return_info', (bool)$flag);
    }

    /**
     * @param AVTaskOption $option
     * @return $this
     */
    public function setAVOptions(AVTaskOption $option)
    {
        return $this->setParam('avopts', (string)$option);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getParams();
    }
}