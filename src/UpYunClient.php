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
     * @param string $bucket_name
     * @param string $username
     * @param string $password
     * @param null|string $endpoint
     * @param int $timeout
     */
    public function __construct($bucket_name, $username, $password, $endpoint = null, $timeout = 30)
    {
        $this->setBucket($bucket_name, $username, $password)
             ->setEndpoint($endpoint)
             ->setTimeout($timeout);
    }

    /**
     * @param string $bucket_name
     * @param string $username
     * @param string $password
     * @return $this
     */
    public function setBucket($bucket_name, $username, $password)
    {
        if (empty($bucket_name) || empty($username) || empty($password)) {
            throw new \InvalidArgumentException('bucket_name|username|password is required.');
        }

        $this->_bucketName = $bucket_name;
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
     * @param bool $mkdir
     * @return array
     * @throws RequestException
     * @throws ResponseException
     */
    public function writeFile($filePath, $body, $options = [], $mkdir = true)
    {
        if (@is_file($body)) {
            $body = file_get_contents($body);
        }
        $length = strlen($body);

        $headers = $this->buildHeaders('PUT', $filePath, $length);
        $headers['mkdir'] = (bool)$mkdir;

        $url = $this->buildRequestUrl($filePath);
        $request = Client::put($url, null, array_merge($headers, $options))
                         ->setContent($body);
        $response = static::send($request);

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
     * @throws RequestException
     * @throws ResponseException
     */
    public function readFile($file)
    {
        $url = $this->buildRequestUrl($file);
        $headers = $this->buildHeaders('GET', $file);

        $request = Client::get($url, null, $headers);
        $response = static::send($request);

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
     * @throws RequestException
     * @throws ResponseException
     */
    public function getFileInfo($file)
    {
        $url = $this->buildRequestUrl($file);
        $headers = $this->buildHeaders('HEAD', $file);

        $request = Client::head($url, null, $headers);
        $response = static::send($request);

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
     * @param bool $mkdir
     * @return bool
     * @throws RequestException
     * @throws ResponseException
     */
    public function createDir($path, $mkdir = true)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('POST', $path);
        $headers['folder'] = true;
        $headers['mkdir'] = (bool)$mkdir;

        $request = Client::post($url, null, $headers);
        $response = static::send($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return true;
        });
    }

    /**
     * @param string $path
     * @return bool
     * @throws RequestException
     * @throws ResponseException
     */
    public function deleteDir($path)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('DELETE', $path);

        $request = Client::delete($url, null, $headers);
        $response = static::send($request);

        return static::handleResponse($response, function (HttpResponse $response) {
            return true;
        });
    }

    /**
     * @param string $path
     * @return array
     * @throws RequestException
     * @throws ResponseException
     */
    public function readDir($path)
    {
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('GET', $path);

        $request = Client::get($url, null, $headers);
        $response = static::send($request);

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
     * @throws RequestException
     * @throws ResponseException
     */
    public function getBucketUsage()
    {
        $path = '/?usage';
        $url = $this->buildRequestUrl($path);
        $headers = $this->buildHeaders('GET', $path);

        $request = Client::get($url, null, $headers);
        $response = static::send($request);

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