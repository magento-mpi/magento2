<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Search change Search Type backend model
 *
 * @category   Magento
 * @package    Magento_CatalogSearch
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Config_Backend_Search_Type extends Magento_Core_Model_Config_Value
{
    /**
     * Catalog search fulltext
     *
     * @var Magento_CatalogSearch_Model_Fulltext
     */
    protected $_catalogSearchFulltext;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param Magento_CatalogSearch_Model_Fulltext $catalogSearchFulltext
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        Magento_CatalogSearch_Model_Fulltext $catalogSearchFulltext,
        array $data = array()
    ) {
        $this->_catalogSearchFulltext = $catalogSearchFulltext;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Search Type process
     *
     * @return Magento_CatalogSearch_Model_Config_Backend_Search_Type|Magento_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->_coreConfig->getValue(
            Magento_CatalogSearch_Model_Fulltext::XML_PATH_CATALOG_SEARCH_TYPE,
            $this->getScope(),
            $this->getScopeId()
        );
        if ($newValue != $oldValue) {
            $this->_catalogSearchFulltext->resetSearchResults();
        }

        return $this;
    }
}
