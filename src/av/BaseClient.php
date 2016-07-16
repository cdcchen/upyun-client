<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/3/13
 * Time: 21:44
 */

namespace cdcchen\upyun\av;


use cdcchen\net\curl\HttpRequest;
use cdcchen\net\curl\HttpResponse;
use cdcchen\upyun\base\Object;
use cdcchen\upyun\base\ParamsTrait;
use cdcchen\upyun\base\RequestException;
use cdcchen\upyun\base\ResponseException;


/**
 * Class BaseClient
 * @package cdcchen\wechat\base
 */
abstract class BaseClient extends Object
{
    use ParamsTrait;

    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $password;

    /**
     * @var string api host url
     */
    protected static $host;


    /**
     * BaseClient constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = md5($password);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * prepare before send request
     */
    public function prepare()
    {
    }


    /**
     * @param BaseRequest $request
     * @return HttpRequest
     */
    private function buildHttpRequest(BaseRequest $request)
    {
        $httpRequest = (new HttpRequest())->setMethod($request->getMethod())
                                          ->setUrl($request->getRequestUrl())
                                          ->addHeaders($request->getHeaders());

        if (is_array($request->getData())) {
            $httpRequest->setData($request->getData());
        } else {
            $httpRequest->setContent($request->getData());
        }

        return $httpRequest;
    }

    /**
     * @param BaseRequest $request
     * @param callable|null $success
     * @return HttpResponse|mixed
     * @throws RequestException
     * @throws ResponseException
     * @throws \cdcchen\net\curl\RequestException
     */
    public function sendRequest(BaseRequest $request, callable $success = null)
    {
        $this->prepare();

        $request->setHost(static::$host)
                ->setAuthorization($this)
                ->validate();

        $httpRequest = $this->buildHttpRequest($request);
        /* @var HttpResponse $response */
        $response = $httpRequest->send();

        $httpCode = (int)$response->getStatus();
        if ($httpCode !== 200) {
            throw new RequestException('Http request error.', $httpCode);
        }

        $data = $response->getData();
        if (isset($data['errcode']) && $data['errcode'] != 0) {
            throw new ResponseException($data['errmsg'], $data['errcode']);
        }

        return $success ? call_user_func($success, $response) : $response;
    }


}