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
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config_Modules $config
     */
    public function __construct(Mage_Core_Model_Config_Modules $config)
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
