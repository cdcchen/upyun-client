<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 11:52
 */

namespace cdcchen\upyun\av;


/**
 * Class FetchFileRequest
 * @package cdcchen\upyun\av
 */
class FetchFileRequest extends BaseRequest
{
    /**
     * @var string
     */
    protected $method = 'post';
    /**
     * @var string
     */
    protected $action = '/pretreatment';


    /**
     * @param $value
     * @return $this
     */
    public function setBucketName($value)
    {
        return $this->setData('bucket_name', $value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function setNotifyUrl($value)
    {
        return $this->setData('notify_url', $value);
    }

    /**
     * @param FetchFileTask[] $tasks
     * @return $this
     */
    public function setTasks(array $tasks)
    {
        foreach ($tasks as $index => $task) {
            $tasks[$index] = $task->toArray();
        }
        return $this->setData('tasks', base64_encode(json_encode($tasks, 320)));
    }

    /**
     * @inheritdoc
     */
    protected function setDefaultParams()
    {
        parent::setDefaultParams();

        $this->setData('app_name', 'spiderman');
    }

    /**
     * @return array
     */
    protected function getRequireParams()
    {
        return ['app_name', 'bucket_name', 'notify_url', 'tasks'];
    }
}