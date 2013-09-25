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
 * Flat category on/off backend
 */
class Magento_Catalog_Model_System_Config_Backend_Catalog_Category_Flat extends Magento_Core_Model_Config_Value
{
    /**
     * Indexer factory
     *
     * @var Magento_Index_Model_IndexerFactory
     */
    protected $_indexerFactory;

    /**
     * Construct
     *
     * @param Magento_Index_Model_IndexerFactory $indexerFactory
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_StoreManager $storeManager
     * @param Magento_Core_Model_Config $config
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Index_Model_IndexerFactory $indexerFactory,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Config $config,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_indexerFactory = $indexerFactory;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After enable flat category required reindex
     *
     * @return Magento_Catalog_Model_System_Config_Backend_Catalog_Category_Flat
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && $this->getValue()) {
            $this->_indexerFactory->create()
                ->getProcessByCode(Magento_Catalog_Helper_Category_Flat::CATALOG_CATEGORY_FLAT_PROCESS_CODE)
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }

        return $this;
    }
}
