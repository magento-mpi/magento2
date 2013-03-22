<?php
/**
 * Gearman helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Queue_Helper_Gearman extends Mage_Core_Helper_Abstract
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
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Helper_Context $context, Mage_Core_Model_Config $config)
    {
        $this->_config = $config;

        parent::__construct($context);
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
        return $result->asArray();
    }
}
