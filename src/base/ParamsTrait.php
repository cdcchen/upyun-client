<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/2
 * Time: 14:44
 */

namespace cdcchen\upyun\base;


/**
 * Class ParamsTrait
 * @package cdcchen\upyun
 */
trait ParamsTrait
{
    /**
     * @var array
     */
    private $_params = [];

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setParam($name, $value)
    {
        $this->_params[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return null|mixed
     */
    public function getParam($name)
    {
        return isset($this->_params[$name]) ? $this->_params[$name] : null;
    }

    /**
     * @param $params
     * @return $this
     */
    public function setParams($params)
    {
        foreach ($params as $name => $value) {
            $this->setParam($name, $value);
        }

        return $this;
    }

    public function getParams()
    {
        return $this->_params;
    }
}