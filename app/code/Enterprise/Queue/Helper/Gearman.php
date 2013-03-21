<?php
/**
 * Gearman helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_Queue_Helper_Gearman extends Mage_Core_Helper_Abstract
{
    /**
     * Configuration XPath of Gearman servers
     */
    const XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS = 'global/queue/adapter/gearman/servers';

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
     * Return a comma-separated list of servers, each server specified in the format host:port
     *
     * @return string
     */
    public function getServers()
    {
        return $this->_config->getNode(self::XML_PATH_QUEUE_ADAPTER_GEARMAN_SERVERS);
    }

    /**
     * Encode data to string before running task in the background
     *
     * @param array $data
     * @return string
     */
    public function encodeData(array $data)
    {
        return json_encode($data);
    }
}
