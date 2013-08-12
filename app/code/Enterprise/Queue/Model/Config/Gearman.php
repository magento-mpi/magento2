<?php
/**
 * Gearman Client configuration
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Model_Config_Gearman implements Magento_JobQueue_Client_ConfigInterface
{
    /**
     * Configuration XPath of Gearman servers
     */
    const XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS = 'global/queue/adapter/gearman/servers';

    /**
     * Application config
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Config_Modules $config)
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
        return (string) $this->_config->getNode(self::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS);
    }
}
