<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/6/16
 * Time: 16:58
 */

namespace cdcchen\upyun\av;


use cdcchen\upyun\base\Object;
use CURLFile;

/**
 * Class BaseRequest
 * @package cdcchen\wechat\base
 */
abstract class BaseRequest extends Object
{
    /**
     * @var string
     */
    protected $method;
    /**
     * @var string
     */
    protected $action;

    /**
     * @var string
     */
    private $_host;
    /**
     * @var mixed
     */
    private $_data;
    /**
     * @var CURLFile[]
     */
    private $_files = [];
    /**
     * @var array
     */
    private $_headers = [];

    /**
     * BaseRequest constructor.
     */
    public function __construct()
    {
        $this->init();
        $this->setDefaultParams();
        $this->setDate();
    }

    /**
     * init
     */
    protected function init()
    {
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setHost($value)
    {
        $this->_host = $value;
        return $this;
    }

    public function setAuthorization(BaseClient $client)
    {
        $this->prepare();

        $authorization = $this->generateSignature($client->getUsername(), $client->getPassword());
        return $this->setHeader('Authorization', $authorization);
    }

    private function setDate()
    {
        return $this->setHeader('Date', gmdate('D, d M Y H:i:s \G\M\T'));
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->_host . '/' . ltrim($this->action, '/');
    }

    /**
     * @param string $name
     * @param string|int $value
     * @return $this
     */
    protected function setHeader($name, $value)
    {
        $this->_headers[$name] = $value;
        return $this;
    }

    public function getHeaders()
    {
        return $this->_headers;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    protected function setData($name, $value)
    {
        $this->_data[$name] = $value;
        return $this;
    }

    /**
     * @param null $name
     * @return mixed|null
     */
    public function getData($name = null)
    {
        if (empty($name)) {
            return $this->_data;
        } elseif (is_array($this->_data)) {
            return isset($this->_data[$name]) ? $this->_data[$name] : null;
        } else {
            throw new \InvalidArgumentException('Data is not a array.');
        }
    }

    /**
     * @param string $value
     * @return $this
     */
    protected function setBody($value)
    {
        $this->_data = $value;
        return $this;
    }

    /**
     * @param $input_name
     * @param array $files
     * @param null $mime_type
     * @param null $post_name
     * @return static
     */
    public function addFiles($input_name, array $files, $mime_type = null, $post_name = null)
    {
        foreach ($files as $index => $file) {
            $inputName = "{$input_name}[{$index}]";
            $this->addFile($inputName, $file, $mime_type, $post_name);
        }

        return $this;
    }

    /**
     * @param $input_name
     * @param $file
     * @param null $mime_type
     * @param null $post_name
     * @return static
     */
    public function addFile($input_name, $file, $mime_type = null, $post_name = null)
    {
        if ($file instanceof CURLFile) {
            $this->_files[$input_name] = $file;
        } else {
            $this->_files[$input_name] = new CURLFile($file, $mime_type, $post_name);
        }

        return $this;
    }

    /**
     * @return static
     */
    public function clearFiles()
    {
        $this->_files = [];
        return $this;
    }

    public function getFiles()
    {
        return $this->_files;
    }

    /**
     * @return bool
     */
    public function isPost()
    {
        return strtolower($this->method) === 'post';
    }

    /**
     * Set default params
     */
    protected function setDefaultParams()
    {
    }

    /**
     * @return array
     */
    protected function getRequireParams()
    {
        return [];
    }

    /**
     * prepare for send
     */
    protected function prepare()
    {
    }

    /**
     * @return bool
     */
    public function validate()
    {
        return $this->checkRequireParams();
    }

    /**
     * @return bool
     */
    private function checkRequireParams()
    {
        $requireParams = (array)$this->getRequireParams();
        if (empty($requireParams)) {
            return true;
        }

        $params = is_array($this->_data) ? $this->_data : [];
        foreach ($requireParams as $param) {
            $parts = explode('|', $param);
            if (isset($parts[1])) {
                foreach ($parts as $part) {
                    if (isset($params[$part])) {
                        continue 2;
                    }
                }
                throw new \InvalidArgumentException("$param cannot at the same time is empty.");
            } elseif (!isset($params[$param])) {
                throw new \InvalidArgumentException("$param is required.");
            }
        }

        return true;
    }

    /**
     * @param string $username
     * @param string $password
     * @return string
     */
    private function generateSignature($username, $password)
    {
        $params = $this->getData() ?: [];
        ksort($params);

        $signatureStr = $username;
        foreach ($params as $name => $value) {
            $signatureStr .= $name . $value;
        }
        $signatureStr .= $password;
        $signature = md5($signatureStr);

        return "UPYUN {$username}:{$signature}";
    }
}