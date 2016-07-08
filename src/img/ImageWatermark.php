<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/2
 * Time: 14:32
 */

namespace cdcchen\upyun\img;


/**
 * Class ImageWatermark
 * @package cdcchen\upyun
 */
class ImageWatermark extends Watermark
{
    /**
     * @param string $url Image absolute path
     */
    public function setUrl($url)
    {
        $this->setParam('url', base64_encode($url));
    }
}