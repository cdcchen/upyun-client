<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 11:52
 */

namespace cdcchen\upyun\av;


/**
 * Class PreTreatmentTaskRequest
 * @package cdcchen\upyun\av
 */
class PreTreatmentTaskRequest extends BaseRequest
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
     * @param string $value
     * @return $this
     */
    public function setAccept($value)
    {
        return $this->setData('accept', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setBucketName($value)
    {
        return $this->setData('bucket_name', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setNotifyUrl($value)
    {
        return $this->setData('notify_url', $value);
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setSource($value)
    {
        return $this->setData('source', $value);
    }

    /**
     * @param array $tasks
     * @return $this
     * @todo Task class not defined
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

        $this->setAccept('json');
    }

    /**
     * @return array
     */
    protected function getRequireParams()
    {
        return ['accept', 'bucket_name', 'notify_url', 'source', 'tasks'];
    }
}