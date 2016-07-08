<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 12:49
 */

namespace cdcchen\upyun\av;


/**
 * Class TaskStatusRequest
 * @package cdcchen\upyun\av
 */
class TaskStatusRequest extends BaseRequest
{
    /**
     * @var string
     */
    protected $method = 'get';
    /**
     * @var string
     */
    protected $action = '/status';

    /**
     * @param string $value
     * @return $this
     */
    public function setBucketName($value)
    {
        return $this->setData('bucket_name', $value);
    }

    /**
     * @param array|string $tasks
     * @return $this
     */
    public function setTaskIds($tasks)
    {
        return $this->setData('task_ids', join(',', (array)$tasks));
    }

    /**
     * @return array
     */
    protected function getRequireParams()
    {
        return ['bucket_name', 'task_ids'];
    }
}