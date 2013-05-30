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
     * Instance of search engine client
     *
     * @var Saas_Search_Model_Adapter_HttpStream|Saas_Search_Model_Adapter_PhpExtension
     */
    protected $_client;

    /**
     * Registry model
     *
     * @var Mage_Core_Model_Registry
     */
    protected $_registryManager;

    /**
     * Logger
     *
     * @var Mage_Core_Model_Logger
     */
    protected $_log;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Registry $registry
     * @param Enterprise_Search_Model_AdapterInterface $client
     * @param Mage_Core_Model_Config_Primary $config
     * @param Mage_Core_Model_Logger $logger
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config_Primary $config,
        Mage_Core_Model_Registry $registry,
        Enterprise_Search_Model_AdapterInterface $client,
        Mage_Core_Model_Logger $logger
    ) {
        parent::__construct($context, $config);
        $this->_log = $logger;
        $this->_registryManager = $registry;
        $this->_client = $client;
    }
    /**
     * Retrieve information from search engine configuration not used store config
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param  string $field
     * @param  int $storeId
     * @return string|int
     * @return Mage_Core_Model_Config_Element
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/' . $field;
        return $this->_config->getNode($path, 'default');
    }

    /**
     * Shows whether replication completed or not
     *
     * @param  string $indexVersion Index version from cache
     * @return bool
     */
    public function isReplicationCompleted($indexVersion)
    {
        try {
            $engineIndexVersion = (string)$this->_client->getIndexVersion();
            if ($engineIndexVersion) {
                $this->_registryManager->register('search_engine_index_version', $engineIndexVersion);
            }
            return ($indexVersion < $engineIndexVersion);
        } catch (Exception $e) {
            $this->_log->logException($e);
        }
        return true;
    }
}
