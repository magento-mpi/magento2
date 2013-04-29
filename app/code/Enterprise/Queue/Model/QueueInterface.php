<?php
/**
 * Queue interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Enterprise_Queue_Model_QueueInterface
{
    const TASK_PRIORITY_HIGH = 'high';
    const TASK_PRIORITY_LOW = 'low';
    const TASK_PRIORITY_MIDDLE = 'middle';

    /**
     * Add task to queue
     *
     * @param string $taskName
     * @param array $params
     * @param string $priority
     */
    public function addTask($taskName, array $params, $priority);

    /**
     * Retrieve task
     *
     * @param string $taskName
     * @param array $params
     * @return bool
     */
    public function getTask($taskName, array $params = array());
}
