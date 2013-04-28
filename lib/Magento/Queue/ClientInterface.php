
<?php
/**
 * Queue server interface
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Magento_Queue_ClientInterface
{
    const TASK_PRIORITY_HIGH = 'high';
    const TASK_PRIORITY_LOW = 'low';
    const TASK_PRIORITY_MIDDLE = 'middle';

    /**
     * Add task to queue
     *
     * @param string $name
     * @param array $params
     * @param mixed $priority
     * @param string $uniqueId
     * @return string
     */
    public function addBackgroundTask($name, $params, $priority = null, $uniqueId = null);
}
