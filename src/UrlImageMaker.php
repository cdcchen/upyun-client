<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/2
 * Time: 13:46
 */

namespace cdcchen\upyun;


/**
 * Class UrlImageMaker
 * @package cdcchen\upyun
 */
class UrlImageMaker extends Object
{
    use ParamsTrait, BoolToStringTrait, ImageMakerTrait;

    const ALIGN_NORTHWEST = 'northwest';
    const ALIGN_NORTH     = 'north';
    const ALIGN_NORTHEAST = 'northeast';
    const ALIGN_EAST      = 'east';
    const ALIGN_SOUTHEAST = 'southeast';
    const ALIGN_SOUTH     = 'south';
    const ALIGN_SOUTHWEST = 'southwest';
    const ALIGN_WEST      = 'west';
    const ALIGN_CENTER    = 'center';

    const FLIP_LEFT_RIGHT = 'left-right';
    const FLIP_TOP_BOTTOM = 'top-bottom';

    const GDORI_TOP_DOWN   = 'top-down';
    const GDORI_BOTTOM_UP  = 'bottom-up';
    const GDORI_LEFT_RIGHT = 'left-right';
    const GDORI_RIGHT_LEFT = 'right-left';

    const EXFORMAT_HEX = 'hex';
    const EXFORMAT_DEX = 'dex';

    const FORMAT_JPG  = 'jpg';
    const FORMAT_PNG  = 'png';
    const FORMAT_WEBP = 'webp';

    /**
     * @var string image thumb version
     */
    protected $version;
    /**
     * @var string
     */
    protected $url;
    /**
     * @var string
     */
    protected $delimiter = '!';

    /**
     * UrlImageMaker constructor.
     * @param string $url
     * @param string $delimiter
     */
    public function __construct($url, $delimiter = null)
    {
        $this->url = $url;
        if ($delimiter) {
            $this->setDelimiter($delimiter);
        }
    }

    /**
     * Build final url
     * @return string
     */
    public function getUrl()
    {
        $url = $this->url;
        $params = static::buildParams($this->getParams());

        if ($this->version || $params) {
            $url .= $this->delimiter . $this->version . $params;
        }

        return $url;

    }

    /**
     * Build fetch image info url
     * @return string
     */
    public function getInfoUrl()
    {
        return $this->url . $this->delimiter . '/info';
    }

    /**
     * Build fetch image meta url
     * @return string
     */
    public function getMetaUrl()
    {
        return $this->url . $this->delimiter . '/meta';
    }

    /**
     * Build fetch image excolor url
     * @param int $n 1-4096
     * @return string
     */
    public function getExColorUrl($n)
    {
        return $this->url . $this->delimiter . "/excolor/{$n}";
    }

    /**
     * Build fetch image info url
     * @param string $flag hex or dex
     * @return string
     */
    public function getExFormat($flag)
    {
        return $this->url . $this->delimiter . "/exformat/{$flag}";
    }

    /**
     * @return array
     */
    public static function aligns()
    {
        return [
            self::ALIGN_CENTER,
            self::ALIGN_EAST,
            self::ALIGN_NORTH,
            self::ALIGN_NORTHEAST,
            self::ALIGN_NORTHWEST,
            self::ALIGN_SOUTH,
            self::ALIGN_SOUTHEAST,
            self::ALIGN_SOUTHWEST,
            self::ALIGN_WEST
        ];
    }

    /**
     * @param array $params
     * @return string
     */
    private static function buildParams($params)
    {
        $paramStr = '';
        foreach ($params as $name => $value) {
            if (is_array($value)) {
                $paramStr .= join('', $value);
            } else {
                $paramStr .= "/$name/$value";
            }
        }

        return $paramStr;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getUrl();
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setVersion($value)
    {
        $this->version = $value;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setDelimiter($value)
    {
        if (empty($value)) {
            throw new \InvalidArgumentException('$delimiter is can not empty.');
        }

        $this->delimiter = $value;
        return $this;
    }


    /**
     * @param int $width
     * @return $this
     */
    public function fw($width)
    {
        if (!is_int($width)) {
            throw new \InvalidArgumentException("Width: {$width} is must be integer.");
        }
        return $this->setParam('fw', $width);
    }

    /**
     * @param int $height
     * @return $this
     */
    public function fh($height)
    {
        if (!is_int($height)) {
            throw new \InvalidArgumentException("Height: {$height} is must be integer.");
        }
        return $this->setParam('fh', $height);
    }

    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function fwfh($width, $height)
    {
        if (!is_int($width) || !is_int($height)) {
            throw new \InvalidArgumentException("Width: {$width} and height: {$height} is must be integer.");
        }

        return $this->setParam('fwfh', "{$width}x{$height}");
    }

    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function both($width, $height)
    {
        if (!is_int($width) || !is_int($height)) {
            throw new \InvalidArgumentException("Width: {$width} and height: {$height} is must be integer.");
        }

        return $this->setParam('both', "{$width}x{$height}");
    }

    /**
     * @param int $length
     * @return $this
     */
    public function sq($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException("Length: {$length} is must be integer.");
        }
        return $this->setParam('sq', $length);
    }

    /**
     * @param int $length
     * @return $this
     */
    public function max($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException("Length: {$length} is must be integer.");
        }
        return $this->setParam('max', $length);
    }

    /**
     * @param int $length
     * @return $this
     */
    public function min($length)
    {
        if (!is_int($length)) {
            throw new \InvalidArgumentException("Length: {$length} is must be integer.");
        }
        return $this->setParam('min', $length);
    }

    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function fxfn($width, $height)
    {
        if (!is_int($width) || !is_int($height)) {
            throw new \InvalidArgumentException("Width: {$width} and height: {$height} is must be integer.");
        }
        return $this->setParam('fxfn', "{$width}x{$height}");
    }

    /**
     * @param int $width
     * @param int $height
     * @return $this
     */
    public function fxfn2($width, $height)
    {
        if (!is_int($width) || !is_int($height)) {
            throw new \InvalidArgumentException("Width: {$width} and height: {$height} is must be integer.");
        }
        return $this->setParam('fxfn2', "{$width}x{$height}");
    }

    /**
     * @param int $value 1-99
     * @return $this
     */
    public function scale($value)
    {
        static::rangeValidate('Scale', $value, 1, 99);
        return $this->setParam('scale', $value);
    }

    /**
     * @param int $value 1-1000
     * @return $this
     */
    public function wscale($value)
    {
        static::rangeValidate('Wscale', $value, 1, 1000);
        return $this->setParam('wscale', $value);
    }

    /**
     * @param int $value 1-1000
     * @return $this
     */
    public function hscale($value)
    {
        static::rangeValidate('Hscale', $value, 1, 1000);
        return $this->setParam('hscale', $value);
    }

    /**
     * @param int
     * @return $this
     */
    public function fp($value)
    {
        return $this->setParam('fp', $value);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function force($flag)
    {
        return $this->setParam('force', $this->booleanToString($flag));
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function crop($width, $height, $x, $y)
    {
        if (!is_int($width) || !is_int($height) || !is_int($x) || !is_int($y)) {
            throw new \InvalidArgumentException("Width: {$width} | height: {$height} | x: {$x} | y: {$y} is must be integer.");
        }
        return $this->setParam('crop', "{$width}x{$height}a{$x}a{$y}");
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function clip($width, $height, $x, $y)
    {
        if (!is_int($width) || !is_int($height) || !is_int($x) || !is_int($y)) {
            throw new \InvalidArgumentException("Width: {$width} | height: {$height} | x: {$x} | y: {$y} is must be integer.");
        }
        return $this->setParam('clip', "{$width}x{$height}a{$x}a{$y}");
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @return $this
     */
    public function gravity($width, $height, $x, $y)
    {
        if (!is_int($width) || !is_int($height) || !is_int($x) || !is_int($y)) {
            throw new \InvalidArgumentException("Width: {$width} | height: {$height} | x: {$x} | y: {$y} is must be integer.");
        }
        return $this->setParam('clip', "{$width}x{$height}a{$x}a{$y}");
    }

    /**
     * @param Watermark $watermark
     * @return $this
     */
    public function setWatermark(Watermark $watermark)
    {
        $watermarks = $this->getParam('watermarks');
        $watermarks[] = $watermark;
        return $this->setParam('watermarks', $watermarks);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function flip($value)
    {
        if (!in_array($value, [self::FLIP_LEFT_RIGHT, self::FLIP_TOP_BOTTOM])) {
            throw new \InvalidArgumentException("Orientation value is left-right or top-bottom");
        }
        return $this->setParam('flip', $value);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function unsharp($flag = true)
    {
        return $this->setParam('unsharp', $this->booleanToString($flag));
    }

    /**
     * @param int $radius
     * @param int $sigma
     * @return $this
     */
    public function gaussblur($radius, $sigma)
    {
        if (!is_int($radius) || !is_int($sigma)) {
            throw new \InvalidArgumentException("Radius: {$radius} and sigma: {$sigma} is must be integer.");
        }
        return $this->setParam('gaussblur', "{$radius}x{$sigma}");
    }

    /**
     * @param int $width
     * @param int $height
     * @param null|static $color
     * @return $this
     */
    public function border($width, $height, $color = null)
    {
        $this->setParam('border', "{$width}x{$height}");
        if ($color) {
            $this->brdcolor($color);
        }
        return $this;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function brdcolor($color)
    {
        if ($this->validateColor($color)) {
            throw new \InvalidArgumentException("Color: {$color} is not valid color.");
        }
        return $this->setParam('brdcolor', $color);
    }

    /**
     * @param int $width
     * @param int $height
     * @param int $x
     * @param int $y
     * @param null|string $color
     * @return $this
     */
    public function canvas($width, $height, $x = 0, $y = 0, $color = null)
    {
        if (!is_int($width) || !is_int($height) || !is_int($x) || !is_int($y)) {
            throw new \InvalidArgumentException("Width: {$width} | height: {$height} | x: {$x} | y: {$y} is must be integer.");
        }
        $this->setParam('canvas', "{$width}x{$height}a{$x}a{$y}");
        if ($color) {
            $this->cvscolor($color);
        }

        return $this;
    }

    /**
     * @param string $color
     * @return $this
     */
    public function cvscolor($color)
    {
        if ($this->validateColor($color)) {
            throw new \InvalidArgumentException("Color: {$color} is not valid color.");
        }
        return $this->setParam('cvscolor', $color);
    }

    /**
     * Gradient
     *
     * @param string $orientation
     * @param string $startColor
     * @param string $stopColor
     * @param int $startPos
     * @param int $endPos
     * @return $this
     */
    public function gradient($orientation, $startColor, $stopColor, $startPos = 0, $endPos = 0)
    {
        $orientations = [self::GDORI_BOTTOM_UP, self::GDORI_LEFT_RIGHT, self::GDORI_RIGHT_LEFT, self::GDORI_TOP_DOWN];
        if (!in_array($orientation, $orientations)) {
            throw new \InvalidArgumentException("Orientation: {$orientation} is not valid value.");
        }
        if (!static::validateColor($startColor) || !static::validateColor($stopColor)) {
            throw new \InvalidArgumentException("StartColor: {$startColor} or StopColor: {$stopColor} is not valid value.");
        }

        if (!is_int($startPos) || !is_int($endPos)) {
            throw new \InvalidArgumentException("Start position: {$startPos} or end position: {$endPos} is not valid value.");
        }

        return $this->setParams([
            'gdori' => $orientation,
            'gdstartcolor' => $startColor,
            'gdstopcolor' => $stopColor,
            'gdpos' => "{$startPos},{$endPos}",
        ]);
    }


    ################## output ########################

    /**
     * @param string $format jpg|png|webp
     * @return $this
     */
    public function format($format)
    {
        $formats = [self::FORMAT_JPG, self::FORMAT_PNG, self::FORMAT_WEBP];
        if (!in_array($format, $formats)) {
            throw new \InvalidArgumentException("Image output format: {$format} is not valid, values: jpg|png|webp.");
        }
        return $this->setParam('format', $format);
    }

    /**
     * @param int $quality
     * @return $this
     */
    public function quality($quality = 75)
    {
        static::rangeValidate('Quality', $quality, 1, 99);
        return $this->setParam('quality', $quality);
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function compress($flag = true)
    {
        return $this->setParam('compress', $this->booleanToString($flag));
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function progressive($flag = true)
    {
        return $this->setParam('progressive', $this->booleanToString($flag));
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function noicc($flag = true)
    {
        return $this->setParam('noicc', $this->booleanToString($flag));
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function strip($flag = true)
    {
        return $this->setParam('strip', $this->booleanToString($flag));
    }

    ################# ext ######################

    /**
     * @param bool $flag
     * @return $this
     */
    public function gifto($flag = true)
    {
        return $this->setParam('gifto', $this->booleanToString($flag));
    }

    /**
     * @param bool $flag
     * @return $this
     */
    public function exifSwitch($flag = true)
    {
        return $this->setParam('exifswitch', $this->booleanToString($flag));
    }
}