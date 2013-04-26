<?php
/**
 * Queue event handler interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Enterprise_Queue_Model_Event_HandlerInterface
{
    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @return Enterprise_Queue_Model_Event_HandlerInterface
     */
    public function addTask($eventName, $data, $priority = null);
}
