<?php
/**
 * Gearman helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Config_Gearman implements Magento_Queue_Client_ConfigInterface
{
    /**
     * Configuration XPath of Gearman servers
     */
    const XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS = 'global/queue/adapter/gearman/servers';

    /**
     * Configuration XPath of Gearman task additional params
     */
    const XML_PATH_QUEUE_ADAPTER_GEARMAN_TASK_PARAMS = 'global/queue/adapter/gearman/task/params';

    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_ConfigInterface $config
     */
    public function __construct(Mage_Core_Model_ConfigInterface $config)
    {
        $this->_config = $config;
    }

    /**
     * Return a comma-separated list of servers, each server specified in the format host:port
     *
     * @return string
     */
    public function getServers()
    {
        return $this->_config->getNode(self::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS);
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
