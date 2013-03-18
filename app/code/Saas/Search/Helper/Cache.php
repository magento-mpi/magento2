<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Saas search helper
 *
 * @category   Saas
 * @package    Saas_Search
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
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Registry $registry
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Registry $registry
    ) {
        parent::__construct($context);
        $this->_registryManager = $registry;
    }
    /**
     * Retrieve information from search engine configuration not used store config
     *
     * @param  string $field
     * @param  int $storeId
     * @return string|int
     * @return Mage_Core_Model_Config_Element
     */
    public function getSearchConfigData($field, $storeId = null)
    {
        $path = 'catalog/search/' . $field;
        return Mage::getConfig()->getNode($path, 'default');
    }

    /**
     * Retrieve instance of search engine client
     *
     * @return Saas_Search_Model_Adapter_HttpStream|Saas_Search_Model_Adapter_PhpExtension
     */
    protected function _getClient()
    {
        if (null === $this->_client) {
            if (extension_loaded('solr')) {
               $model = 'Saas_Search_Model_Adapter_PhpExtension';
            } else {
               $model = 'Saas_Search_Model_Adapter_HttpStream';
            }
            $this->_client = Mage::getModel($model, $this->getSolrServers());
        }
        return $this->_client;
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
            $engineIndexVersion = (string)$this->_getClient()->getIndexVersion();
            if ($engineIndexVersion) {
                $this->_registryManager->register('search_engine_index_version', $engineIndexVersion);
            }
            return ($indexVersion < $engineIndexVersion);
        } catch (Exception $e) {
           Mage::log($e);
        }
        return true;
    }
}
