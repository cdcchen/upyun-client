<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 15/5/22
 * Time: 下午11:26
 */

namespace cdcchen\upyun;


use cdcchen\net\curl\Client;
use cdcchen\net\curl\HttpResponse;
use cdcchen\upyun\base\BaseClient;

/**
 * Class UpYunClient
 * @package cdcchen\upyun
 */
class UpYunClient extends BaseClient
{
    /**
     * auto chose endpoint
     */
    const ENDPOINT_AUTO = 'v0.api.upyun.com';
    /**
     * telecom
     */
    const ENDPOINT_TELECOM = 'v1.api.upyun.com';
    /**
     * unicom
     */
    const ENDPOINT_UNICOM = 'v2.api.upyun.com';
    /**
     * cmcc
     */
    const ENDPOINT_CMCC = 'v3.api.upyun.com';

    /**
     * content-type header name
     */
    const CONTENT_TYPE = 'Content-Type';
    /**
     * content-md5 header name
     */
    const CONTENT_MD5 = 'Content-MD5';
    /**
     * content-secret header name
     */
    const CONTENT_SECRET = 'Content-Secret';

    // thumbnail
    const X_GMKERL_THUMBNAIL = 'x-gmkerl-thumbnail';
    const X_GMKERL_TYPE      = 'x-gmkerl-type';
    const X_GMKERL_VALUE     = 'x-gmkerl-value';
    const X_GMKERL_QUALITY   = 'x­gmkerl-quality';
    const X_GMKERL_UNSHARP   = 'x­gmkerl-unsharp';

    /**
     * @var string
     */
    private $_scheme = 'http';
    /**
     * @var string
     */
    private $_bucketName;
    /**
     * @var string
     */
    private $_username;
    /**
     * @var string
     */
    private $_password;
    /**
     * @var int
     */
    private $_timeout = 30;

    /**
     * @var string
     */
    private $_endpoint;


    /**
     * UpYunClient constructor.
     * @param string $bucketName
     * @param string $username
     * @param string $password
     * @param null|string $endpoint
     * @param int $timeout
     */
    public function __construct($bucketName, $username, $password, $endpoint = null, $timeout = 30)
    {
        $this->setBucket($bucketName, $username, $password)
             ->setEndpoint($endpoint)
             ->setTimeout($timeout);
    }

    /**
     * @param string $bucketName
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setBucket($bucketName, $username, $password)
    {
        if (empty($bucketName) || empty($username) || empty($password)) {
            throw new \InvalidArgumentException('bucket_name|username|password is required.');
        }

        $this->_bucketName = $bucketName;
        $this->_username = $username;
        $this->_password = md5($password);

        return $this;
    }

    /**
     * @param string $endpoint
     * @return $this
     */
    public function setEndpoint($endpoint)
    {
        $this->_endpoint = $endpoint ?: self::ENDPOINT_AUTO;
        return $this;
    }

    /**
     * @param int $seconds
     * @return $this
     */
    public function setTimeout($seconds)
    {
        $this->_timeout = $seconds;
        return $this;
    }

    /**
     * @param string $filePath
     * @param string $body
     * @param array $options
     * @param bool $mkDir
     * @return array
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function writeFile($filePath, $body, $options = [], $mkDir = true)
    {
        if (@is_file($body)) {
            $body = file_get_contents($body);
        }
        $length = strlen($body);

        $headers = $this->buildHeaders('PUT', $filePath, $length);
        $headers['mkdir'] = (bool)$mkDir;

        $url = $this->buildRequestUrl($filePath);
        $request = Client::put($url, null, array_merge($headers, $options))
                         ->setContent($body);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return static::parseWriteFileResponse($response);
        });
    }

    /**
     * @param HttpResponse $response
     * @return array|bool
     */
    private static function parseWriteFileResponse(HttpResponse $response)
    {
        if ($response->hasHeader('x-upyun-width')) {
            $headers = $response->getHeaders();
            return [
                'width' => (int)$headers['x-upyun-width'],
                'height' => (int)$headers['x-upyun-height'],
                'type' => $headers['x-upyun-file-type'],
                'frames' => (int)$headers['x-upyun-frames'],
            ];
        }

        return true;
    }

    /**
     * @param string $file
     * @return string
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function readFile($file)
    {
        $url = $this->buildRequestUrl($file);
        $headers = $this->buildHeaders('GET', $file);

        $request = Client::get($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return $response->getContent();
        });
    }

    /**
     * @param string $file
     * @return bool
     */
    public function deleteFile($file)
    {
        return $this->deleteDir($file);
    }

    /**
     * @param string $file
     * @return array
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function getFileInfo($file)
    {
        $url = $this->buildRequestUrl($file);
        $headers = $this->buildHeaders('HEAD', $file);

        $request = Client::head($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return static::parseFileInfoResponse($response);
        });
    }

    /**
     * @param HttpResponse $response
     * @return array
     */
    private static function parseFileInfoResponse(HttpResponse $response)
    {
        $headers = $response->getHeaders();
        return [
            'type' => $headers['x-upyun-file-type'],
            'size' => (int)$headers['x-upyun-file-size'],
            'date' => (int)$headers['x-upyun-file-date'],
        ];
    }

    /**
     * @param string $path
     * @param bool $mkDir
     * @return bool
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function createDir($path, $mkDir = true)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('POST', $path);
        $headers['folder'] = true;
        $headers['mkdir'] = (bool)$mkDir;

        $request = Client::post($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return true;
        });
    }

    /**
     * @param string $path
     * @return bool
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function deleteDir($path)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('DELETE', $path);

        $request = Client::delete($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return true;
        });
    }

    /**
     * @param string $path
     * @return array
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function readDir($path)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('GET', $path);

        $request = Client::get($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return static::parseReadDirResponse($response);
        });
    }

    /**
     * @param HttpResponse $response
     * @return array
     */
    private static function parseReadDirResponse(HttpResponse $response)
    {
        $body = $response->getContent();
        $lines = explode("\n", $body);
        $files = [];

        foreach ($lines as $line) {
            list($name, $type, $size, $date) = explode("\t", $line);
            $files[] = [
                'name' => $name,
                'type' => $type,
                'size' => (int)$size,
                'date' => (int)$date,
            ];
        }
        return $files;
    }

    /**
     * @return string
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function getBucketUsage()
    {
        $path = '/?usage';
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('GET', $path);

        $request = Client::get($url, null, $headers);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return $response->getContent();
        });
    }

    /**
     * @param string|array $urls
     * @return mixed
     * @throws \cdcchen\upyun\base\RequestException
     * @throws \cdcchen\upyun\base\ResponseException
     */
    public function purgeUrl($urls)
    {
        $request = new PurgeRequest();
        $request->buildHeaders($urls, $this->_bucketName, $this->_username, $this->_password);
        $request->setData(['purge' => join("\n", (array)$$urls)]);
        $response = static::sendRequest($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return $response->getContent();
        });
    }

    /**
     * @param string $path
     * @return string
     */
    private function buildRequestUrl($path)
    {
        $path = $this->buildRequestPath($path);
        return "{$this->_scheme}://{$this->_endpoint}{$path}";
    }

    /**
     * @param string $path
     * @return string
     */
    private function buildRequestPath($path)
    {
        return '/' . $this->_bucketName . '/' . ltrim($path, '/');
    }

    /**
     * @param string $method
     * @param string $path
     * @param int $length
     * @return array
     */
    private function buildHeaders($method, $path, $length = 0)
    {
        $uri = $this->buildRequestPath($path);
        $date = $this->getDate();
        $length = (int)$length;
        $sign = $this->generateSignature($method, $uri, $date, $length);

        return [
            'Expect:',
            "Authorization: {$sign}",
            "Date: {$date}",
            "Content-Length: {$length}",
        ];
    }

    /**
     * @return string
     */
    private function getDate()
    {
        return gmdate('D, d M Y H:i:s \G\M\T');
    }

    /**
     * @param string $method
     * @param string $uri
     * @param string $date
     * @param int $length
     * @return string
     */
    private function generateSignature($method, $uri, $date, $length)
    {
        $method = strtoupper($method);
        $sign = "{$method}&{$uri}&{$date}&{$length}&{$this->_password}";

        return 'UpYun ' . $this->_username . ':' . md5($sign);
    }
}