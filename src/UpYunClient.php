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

class UpYunClient extends BaseClient
{
    const ENDPOINT_AUTO    = 'v0.api.upyun.com';
    const ENDPOINT_TELECOM = 'v1.api.upyun.com';
    const ENDPOINT_UNICOM  = 'v2.api.upyun.com';
    const ENDPOINT_CMCC    = 'v3.api.upyun.com';

    const CONTENT_TYPE   = 'Content-Type';
    const CONTENT_MD5    = 'Content-MD5';
    const CONTENT_SECRET = 'Content-Secret';

    // 缩略图
    const X_GMKERL_THUMBNAIL = 'x-gmkerl-thumbnail';
    const X_GMKERL_TYPE      = 'x-gmkerl-type';
    const X_GMKERL_VALUE     = 'x-gmkerl-value';
    const X_GMKERL_QUALITY   = 'x­gmkerl-quality';
    const X_GMKERL_UNSHARP   = 'x­gmkerl-unsharp';
    /*}}}*/

    private $_scheme  = 'http';
    private $_bucketName;
    private $_username;
    private $_password;
    private $_timeout = 30;

    private $_endpoint;


    public function __construct($bucket_name, $username, $password, $endpoint = null, $timeout = 30)
    {
        $this->_bucketName = $bucket_name;
        $this->_username = $username;
        $this->_password = md5($password);
        $this->_timeout = $timeout;

        $this->_endpoint = is_null($endpoint) ? self::ENDPOINT_AUTO : $endpoint;
    }

    public function writeFile($filePath, $body, $options = [], $mkdir = true)
    {
        if (is_file($body)) {
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

    private static function parseWriteFileResponse(HttpResponse $response)
    {
        $headers = $response->getHeaders();
        return [
            'width' => (int)$headers['x-upyun-width'],
            'height' => (int)$headers['x-upyun-height'],
            'type' => $headers['x-upyun-file-type'],
            'frames' => (int)$headers['x-upyun-frames'],
        ];
    }

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

    public function deleteFile($file)
    {
        return $this->deleteDir($file);
    }

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

    private static function parseFileInfoResponse(HttpResponse $response)
    {
        $headers = $response->getHeaders();
        return [
            'type' => $headers['x-upyun-file-type'],
            'size' => (int)$headers['x-upyun-file-size'],
            'date' => (int)$headers['x-upyun-file-date'],
        ];
    }

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

    private function buildRequestUrl($path)
    {
        $path = $this->buildRequestPath($path);
        return "{$this->_scheme}://{$this->_endpoint}{$path}";
    }

    private function buildRequestPath($path)
    {
        return '/' . $this->_bucketName . '/' . ltrim($path, '/');
    }

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

    private function getDate()
    {
        return gmdate('D, d M Y H:i:s \G\M\T');
    }

    private function generateSignature($method, $uri, $date, $length)
    {
        $method = strtoupper($method);
        $sign = "{$method}&{$uri}&{$date}&{$length}&{$this->_password}";

        return 'UpYun ' . $this->_username . ':' . md5($sign);
    }
}