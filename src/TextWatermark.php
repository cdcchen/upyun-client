<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/2
 * Time: 14:32
 */

namespace cdcchen\upyun;


/**
 * Class TextWatermark
 * @package cdcchen\upyun
 */
class TextWatermark extends Watermark
{
    const FONT_SIMSUN    = 'simsun';
    const FONT_SIMHEI    = 'simhei';
    const FONT_SIMKAI    = 'simkai';
    const FONT_SIMLI     = 'simli';
    const FONT_SIMYOU    = 'simyou';
    const FONT_SIMFANG   = 'simfang';
    const FONT_SC        = 'sc';
    const FONT_TC        = 'tc';
    const FONT_ARIAL     = 'arial';
    const FONT_GEORGIA   = 'georgia';
    const FONT_HELVETICA = 'helvetica';
    const FONT_ROMAN     = 'roman';

    /**
     * @return array
     */
    public static function fonts()
    {
        return [
            self::FONT_ARIAL,
            self::FONT_GEORGIA,
            self::FONT_HELVETICA,
            self::FONT_ROMAN,
            self::FONT_SIMFANG,
            self::FONT_SIMHEI,
            self::FONT_SIMKAI,
            self::FONT_SIMLI,
            self::FONT_SIMSUN,
            self::FONT_SIMYOU,
            self::FONT_SC,
            self::FONT_TC
        ];
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText($text)
    {
        return $this->setParam('text', base64_encode($text));
    }

    /**
     * @param int $size
     * @return $this
     */
    public function setSize($size)
    {
        if (!is_int($size)) {
            throw new \InvalidArgumentException('Font size must be an integer');
        }

        return $this->setParam('size', $size);
    }

    /**
     * @param string $font
     * @return $this
     */
    public function setFont($font)
    {
        if (!in_array($font, static::fonts())) {
            throw new \InvalidArgumentException("Font family: $font is not a valid value.");
        }
        return $this->setParam('font', $font);
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setColor($color)
    {
        if (static::validateColor($color)) {
            throw new \InvalidArgumentException("$color is not a valid hex color value");
        }
        return $this->setParam('color', $color);
    }

    /**
     * @param string $color
     * @return $this
     */
    public function setBorder($color)
    {
        if (static::validateAlphaColor($color)) {
            throw new \InvalidArgumentException("$color is not a valid hex alpha color value");
        }
        return $this->setParam('border', $color);
    }
}