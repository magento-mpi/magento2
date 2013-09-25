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
 * Catalog Config Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Config extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * catalog_product entity type id
     *
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Store id
     *
     * @var int
     */
    protected $_storeId          = null;

    /**
     * Eav config
     *
     * @var Magento_Eav_Model_Config
     */
    protected $_eavConfig;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Class constructor
     *
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Core_Model_Resource $resource
     */
    public function __construct(
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Core_Model_Resource $resource
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('eav_attribute', 'attribute_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return Magento_Catalog_Model_Resource_Config
     */
    public function setStoreId($storeId)
    {
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return store id.
     * If is not set return current app store
     *
     * @return integer
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            return $this->_storeManager->getStore()->getId();
        }
        return $this->_storeId;
    }

    /**
     * Retrieve catalog_product entity type id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = $this->_eavConfig->getEntityType(Magento_Catalog_Model_Product::ENTITY)->getId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve Product Attributes Used in Catalog Product listing
     *
     * @return array
     */
    public function getAttributesUsedInListing()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select  = $adapter->select()
            ->from(array('main_table' => $this->getTable('eav_attribute')))
            ->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'main_table.attribute_id = additional_table.attribute_id'
            )
            ->joinLeft(
                array('al' => $this->getTable('eav_attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                array('store_label' => $storeLabelExpr)
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_in_product_listing = ?', 1);

        return $adapter->fetchAll($select);
    }

    /**
     * Retrieve Used Product Attributes for Catalog Product Listing Sort By
     *
     * @return array
     */
    public function getAttributesUsedForSortBy()
    {
        $adapter = $this->_getReadAdapter();
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NULL', 'main_table.frontend_label','al.value');
        $select = $adapter->select()
            ->from(array('main_table' => $this->getTable('eav_attribute')))
            ->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'main_table.attribute_id = additional_table.attribute_id',
                array()
            )
            ->joinLeft(
                array('al' => $this->getTable('eav_attribute_label')),
                'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
                array('store_label' => $storeLabelExpr)
            )
            ->where('main_table.entity_type_id = ?', (int)$this->getEntityTypeId())
            ->where('additional_table.used_for_sort_by = ?', 1);

        return $adapter->fetchAll($select);
    }
}
