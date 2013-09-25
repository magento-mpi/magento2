<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Flat product on/off backend
 */
class Magento_Catalog_Model_System_Config_Backend_Catalog_Product_Flat extends Magento_Core_Model_Config_Value
{
    /**
     * Index indexer
     *
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexIndexer;

    /**
     * Construct
     *
     * @param Magento_Index_Model_Indexer $indexIndexer
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Index_Model_Indexer $indexIndexer,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexIndexer = $indexIndexer;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After enable flat products required reindex
     *
     * @return Magento_Catalog_Model_System_Config_Backend_Catalog_Product_Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            $this->_indexIndexer->getProcessByCode('catalog_product_flat')
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
