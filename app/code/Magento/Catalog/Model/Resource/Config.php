<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Resource;

/**
 * Catalog Config Resource Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Config extends \Magento\Model\Resource\Db\AbstractDb
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
    protected $_storeId = null;

    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;
        parent::__construct($resource);
    }

    /**
     * Initialize connection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('eav_attribute', 'attribute_id');
    }

    /**
     * Set store id
     *
     * @param integer $storeId
     * @return $this
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
            $this->_entityTypeId = $this->_eavConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)->getId();
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

        $select = $adapter->select()->from(
            array('main_table' => $this->getTable('eav_attribute'))
        )->join(
            array('additional_table' => $this->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id'
        )->joinLeft(
            array('al' => $this->getTable('eav_attribute_label')),
            'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
            array('store_label' => $storeLabelExpr)
        )->where(
            'main_table.entity_type_id = ?',
            (int)$this->getEntityTypeId()
        )->where(
            'additional_table.used_in_product_listing = ?',
            1
        );

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
        $storeLabelExpr = $adapter->getCheckSql('al.value IS NULL', 'main_table.frontend_label', 'al.value');
        $select = $adapter->select()->from(
            array('main_table' => $this->getTable('eav_attribute'))
        )->join(
            array('additional_table' => $this->getTable('catalog_eav_attribute')),
            'main_table.attribute_id = additional_table.attribute_id',
            array()
        )->joinLeft(
            array('al' => $this->getTable('eav_attribute_label')),
            'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
            array('store_label' => $storeLabelExpr)
        )->where(
            'main_table.entity_type_id = ?',
            (int)$this->getEntityTypeId()
        )->where(
            'additional_table.used_for_sort_by = ?',
            1
        );

        return $adapter->fetchAll($select);
    }
}
