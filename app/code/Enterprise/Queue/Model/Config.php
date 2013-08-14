<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Enterprise_Queue_Model_Config
{
    /**
     * Configuration XPath of Gearman task additional params
     */
    const XML_PATH_QUEUE_ADAPTER_GEARMAN_TASK_PARAMS = 'global/queue/adapter/gearman/task/params';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Model_Config $config
     */
    public function __construct(Magento_Core_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Return additional params for every task
     *
     * @return array
     */
    public function getTaskParams()
    {
        $result = $this->_config->getNode(self::XML_PATH_QUEUE_ADAPTER_GEARMAN_TASK_PARAMS);
        if ($result === false) {
            return array();
        }
        return (array)$result->asArray();
    }
}
