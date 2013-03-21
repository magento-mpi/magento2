<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Queue adapter interface
 */
interface Enterprise_Queue_Model_Queue_Adapter_AdapterInterface
{
    /**
     * Add task to queue
     *
     * @param string $eventName
     * @param array $data
     * @param string|null $priority
     * @return Enterprise_Queue_Model_Queue_Adapter_AdapterInterface
     */
    public function addTask($eventName, $data, $priority = null);
}
