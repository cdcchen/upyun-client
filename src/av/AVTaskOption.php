<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/20
 * Time: 20:37
 */

namespace cdcchen\upyun\av;


use cdcchen\upyun\base\Object;
use cdcchen\upyun\base\ParamsTrait;

/**
 * Class AVTaskOption
 * @package cdcchen\upyun\av
 */
class AVTaskOption extends Object
{
    use ParamsTrait;

    /**
     * @param string $format
     * @return $this
     */
    public function setFormat($format)
    {
        return $this->setParam('f', $format);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setAudioChannel($value)
    {
        return $this->setParam('ac', $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setAudioBitrate($value)
    {
        return $this->setParam('ab', $value);
    }

    /**
     * @param int $value
     * @return $this
     */
    public function setAudioVbr($value)
    {
        // @todo 验证参数
        return $this->setParam('audio_vbr', $value);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function setMapMetadata($flag = true)
    {
        return $this->setParam('sm', (int)(bool)$flag);
    }

    public function setStartTime($time)
    {
        return $this->setParam('ss', $time);
    }

    public function setEndTime($time)
    {
        return $this->setParam('es', $time);
    }

    public function setConcat($media)
    {
        $value = $this->getParam('i');
        $encodeMedia = base64_encode($media);
        if ($value === null) {
            $value = $encodeMedia;
        } else {
            $value = (array)$value;
            array_push($value, $encodeMedia);
        }

        return $this->setParam('i', $value);
    }

    public function setThumbScale($width, $height)
    {
        return $this->setParam('s', $width . ':' . $height);
    }

    public function setThumbAmount($value)
    {
        return $this->setParam('n', $value);
    }

    public function setThumbSingle($flag)
    {
        return $this->setParam('o', (int)(bool)$flag);
    }

    public function setWaterMarkerImage($file)
    {
        return $this->setParam('wmImg', base64_encode($file));
    }

    public function setWaterMarkerGravity($position)
    {
        return $this->setParam('wmGravity', $position);
    }

    public function setWaterMarkerDx($value)
    {
        return $this->setParam('wmDx', $value);
    }

    public function setWaterMarkerDy($value)
    {
        return $this->setParam('wmDy', $value);
    }

    public function setHlsTime($time)
    {
        return $this->setParam('ht', $time);
    }

    public function setVideoBitrate($value)
    {
        return $this->setParam('vb', $value);
    }

    public function setAutoScale($scale)
    {
        return $this->setParam('as', $scale);
    }

    public function setFrameRate($rate)
    {
        return $this->setParam('r', $rate);
    }

    public function setVideoRotate($rotate)
    {
        return $this->setParam('sp', $rotate);
    }

    public function setAudioCodec($codec)
    {
        return $this->setParam('acodec', $codec);
    }

    public function setVideoCodec($codec)
    {
        return $this->setParam('vcodec', $codec);
    }

    public function setDisableAudio($flag)
    {
        return $this->setParam('an', (int)(bool)$flag);
    }

    public function setDisableVideo($flag)
    {
        return $this->setParam('vn', (int)(bool)$flag);
    }

    public function setAccelerateFactor($value)
    {
        return $this->setParam('su', $value);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $this->validate();

        $options = '';
        foreach ($this->getParams() as $key => $value) {
            $keyStr = '/' . $key . '/';
            if (is_array($value)) {
                $options .= $keyStr . join($keyStr, $value);
            } else {
                $options .= $keyStr . $value;
            }
        }

        return $options;
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function validate()
    {
    }
}