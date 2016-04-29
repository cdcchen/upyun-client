<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/4/29
 * Time: 10:00
 */

namespace cdcchen\upyun;


use Exception;
use cdcchen\net\curl\HttpRequest;
use cdcchen\net\curl\HttpResponse;

abstract class BaseClient
{
    /**
     * @param HttpRequest $request
     * @param callable|null $success
     * @param callable|null $failed
     * @return bool|\cdcchen\net\curl\HttpResponse
     * @throws RequestException
     */
    protected static function send(HttpRequest $request, callable $success = null, callable $failed = null)
    {
        try {
            $response = $request->send();
            if ($success === null) {
                return $response;
            } else {
                return call_user_func($success, $response);
            }
        } catch (\Exception $e) {
            if ($failed) {
                return call_user_func($failed, $request);
            } else {
                throw new RequestException($e->getMessage(), $e->getCode());
            }
        }
    }

    /**
     * @param HttpResponse $response
     * @param callable|null $success
     * @param callable|null $failed
     * @return mixed
     * @throws ResponseException
     */
    protected static function handleResponse(HttpResponse $response, callable $success = null, callable $failed = null)
    {
        $httpCode = (int)$response->getHeader('http-code');
        if ($httpCode === 200) {
            return call_user_func($success, $response);
        } else {
            if ($failed) {
                return call_user_func($failed, $response);
            } else {
                $data = $response->getData();
                throw new ResponseException($data['msg'], $data['code']);
            }
        }
    }
}