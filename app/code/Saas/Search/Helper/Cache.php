<?php
/**
 * Cashe Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Search_Helper_Cache extends Saas_Search_Helper_Data
{
    /**
     * Path to config search data
     */
    const XML_SEARCH_CONFIG_DATA_PATH = 'default/catalog/search/';

    /**
     * Instance of search engine client
     *
     * @var Saas_Search_Model_Client_Balancer_HttpStream|Saas_Search_Model_Client_Balancer_PhpExtension
     */
    protected $_client;

    /**
     * Metadata model
     *
     * @var Enterprise_PageCache_Model_Metadata
     */
    protected $_metadata;

    /**
     * Logger
     *
     * @var Magento_Core_Model_Logger
     */
    protected $_log;

    /**
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config_Primary $config
     * @param Enterprise_PageCache_Model_Metadata $metadata
     * @param Enterprise_Search_Model_Client_FactoryInterface $clientFactory
     * @param Magento_Core_Model_Logger $logger
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config_Primary $config,
        Enterprise_PageCache_Model_Metadata $metadata,
        Enterprise_Search_Model_Client_FactoryInterface $clientFactory,
        Magento_Core_Model_Logger $logger
    ) {
        parent::__construct($context, $config);
        $this->_log = $logger;
        $this->_metadata = $metadata;
        $this->_client = $clientFactory->createClient($this->getSolrServers());
    }

    /**
     * Retrieve information from search engine configuration not used store config
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  string $field
     * @param  int $storeId
     * @return Magento_Core_Model_Config_Element
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = self::XML_SEARCH_CONFIG_DATA_PATH . $field;
        return $this->_config->getNode($path);
    }

    /**
     * Shows whether replication completed or not
     *
     * @return bool
     */
    public function isReplicationCompleted()
    {
        try {
            $indexVersion = $this->_metadata->getMetadata('search_engine_index_version');
            $engineIndexVersion = $this->_client->getIndexVersion();
            if ($engineIndexVersion) {
                $this->_metadata->setMetadata('search_engine_index_version', $engineIndexVersion);
                $this->_metadata->saveMetadata();
            }
            return ($indexVersion < $engineIndexVersion);
        } catch (Exception $e) {
            $this->_log->logException($e);
        }
        return false;
    }
}
