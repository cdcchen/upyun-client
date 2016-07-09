<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 09:43
 */

namespace cdcchen\upyun;


use cdcchen\net\curl\HttpResponse;
use cdcchen\upyun\av\BaseClient;
use cdcchen\upyun\av\FetchFileRequest;
use cdcchen\upyun\av\FetchFileTask;
use cdcchen\upyun\av\PreTreatmentTaskRequest;
use cdcchen\upyun\av\TaskResultRequest;
use cdcchen\upyun\av\TaskStatusRequest;

/**
 * Class AVClient
 * @package cdcchen\upyun
 */
class AVClient extends BaseClient
{
    /**
     * api host url
     */
    protected static $host = 'http://p0.api.upyun.com';

    /**
     * @param string $bucketName
     * @param string $notifyUrl
     * @param string $source
     * @param array $tasks
     * @return string
     * @throws base\RequestException
     * @throws base\ResponseException
     */
    public function createPreTreatmentTask($bucketName, $notifyUrl, $source, $tasks)
    {
        $request = new PreTreatmentTaskRequest();
        $request->setBucketName($bucketName)
                ->setNotifyUrl($notifyUrl)
                ->setSource($source)
                ->setTasks($tasks);

        return $this->sendRequest($request, function (HttpResponse $response) {
            return $response->getData();
        });
    }

    /**
     * @param $bucket
     * @param $tasks
     * @return array
     * @throws base\RequestException
     * @throws base\ResponseException
     */
    public function queryTaskStatus($bucket, $tasks)
    {
        $request = new TaskStatusRequest();
        $request->setBucketName($bucket)->setTaskIds($tasks);

        return $this->sendRequest($request, function (HttpResponse $response) {
            $data = $response->getData();
            return $data['tasks'];
        });
    }

    /**
     * @param $bucket
     * @param $tasks
     * @return array
     * @throws base\RequestException
     * @throws base\ResponseException
     */
    public function queryTaskResult($bucket, $tasks)
    {
        $request = new TaskResultRequest();
        $request->setBucketName($bucket)->setTaskIds($tasks);

        return $this->sendRequest($request, function (HttpResponse $response) {
            $data = $response->getData();
            return $data['tasks'];
        });
    }

    public function fetchFiles($bucketName, array $tasks, $notifyUrl)
    {
        $request = new FetchFileRequest();
        $request->setBucketName($bucketName)->setNotifyUrl($notifyUrl)->setTasks($tasks);

        return $this->sendRequest($request, function (HttpResponse $response) {
            return $response->getData();
        });
    }

    public function fetchFile($bucketName, $notifyUrl, $url, $saveAs, $random = false, $overwrite = true)
    {
        $task = (new FetchFileTask())->setUrl($url)
                                     ->setSaveAs($saveAs)
                                     ->setRandom($random)
                                     ->setOverwrite($overwrite);

        $request = new FetchFileRequest();
        $request->setBucketName($bucketName)->setNotifyUrl($notifyUrl)->setTasks([$task]);

        return $this->sendRequest($request, function (HttpResponse $response) {
            return $response->getData();
        });
    }
}