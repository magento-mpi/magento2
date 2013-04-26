
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
    /**
     * Add task to queue
     *
     * @param string $name
     * @param array $params
     * @param mixed $priority
     * @return string
     */
    public function addTask($name, $params, $priority = null);
}
