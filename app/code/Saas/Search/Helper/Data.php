<?php
/**
 * Saas Search Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Helper_Data extends Enterprise_Search_Helper_Data
{
    /**
     * @var Mage_Core_Model_ConfigInterface
     */
    protected $_config;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config_Primary $config
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config_Primary $config
    ) {
        parent::__construct($context);
        $this->_config = $config;
    }

    /**
     * Retrieve Solr servers config
     *
     * @return array
     */
    public function getSolrServers()
    {
        $path = (string)$this->getSolrConfigData('server_path');
        $servers = $this->_config->getNode('global/search/solr/servers')->asArray();
        $serversArray = array();
        foreach ($servers as $type => $group) {
            foreach ($group as $server) {
                $serversArray[$type][] = array(
                    "host"    => $server['host'],
                    "port"    => $server['port'],
                    "timeout" => $server['timeout'],
                    "path"    => $path,
                    "login"   => false,
                    "password"=> false,
                );
            }
        }
        return $serversArray;
    }

    /**
     * Return search client options
     *
     * @param $options
     * @return mixed
     */
    public function prepareClientOptions($options = array())
    {
        return array_merge($this->getSolrServers(), $options);
    }
}
