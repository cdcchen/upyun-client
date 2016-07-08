<?php
/**
 * Created by PhpStorm.
 * User: chendong
 * Date: 16/7/8
 * Time: 12:49
 */

namespace cdcchen\upyun\av;


/**
 * Class TaskResultRequest
 * @package cdcchen\upyun\av
 */
class TaskResultRequest extends BaseRequest
{
    /**
     * @var string
     */
    protected $method = 'get';
    /**
     * @var string
     */
    protected $action = '/result';

    /**
     * @param string $value
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
    public function setTaskIds($value)
    {
        return $this->setData('task_ids', $value);
    }

    /**
     * @return array
     */
    protected function getRequireParams()
    {
        return ['bucket_name', 'task_ids'];
    }
}