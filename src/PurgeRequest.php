<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/1
 * Time: 14:35
 */

namespace cdcchen\upyun;


use cdcchen\net\curl\HttpRequest;
use cdcchen\net\curl\Request;

/**
 * Class PurgeRequest
 * @package cdcchen\upyun
 */
class PurgeRequest extends HttpRequest
{
    /**
     * purge url
     */
    const PURGE_URL = 'http://purge.upyun.com/purge/';

    /**
     * @param string|array $urls
     * @param string $bucket
     * @param string $username
     * @param string $password
     */
    public function buildHeaders($urls, $bucket, $username, $password)
    {
        $urlStr = join("\n", (array)$$urls);
        $date = gmdate('D, d M Y H:i:s \G\M\T');
        $sign = $this->generateSignature($urlStr, $bucket, $date, $username, $password);

        $this->addHeaders([
            "Authorization: {$sign}",
            "Date: {$date}",
        ]);
    }

    /**
     * @param string $urlStr
     * @param string $bucket
     * @param string $date
     * @param string $username
     * @param string $password
     * @return string
     */
    private function generateSignature($urlStr, $bucket, $date, $username, $password)
    {
        $signStr = "{$urlStr}&{$bucket}&{$date}&{$password}";

        return "UpYun {$bucket}:{$username}:" . md5($signStr);
    }

    /**
     * @param Request $request
     * @param resource $handle
     * @return bool
     */
    protected function beforeRequest(Request $request, $handle)
    {
        $this->setUrl(self::PURGE_URL)->setMethod('post');

        return true;
    }
}