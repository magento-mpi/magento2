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
 * Catalog Product Flat resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Resource\Product;

class Flat extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Store scope Id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Class constructor
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $catalogConfig
     * @param \Magento\Core\Model\Resource $resource
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $catalogConfig,
        \Magento\Core\Model\Resource $resource
    ) {
        $this->_storeManager = $storeManager;
        $this->_catalogConfig = $catalogConfig;
        parent::__construct($resource);
    }

    /**
     * Init connection and resource table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_flat', 'entity_id');
        $this->_storeId = (int)$this->_storeManager->getStore()->getId();
    }

    /**
     * Retrieve store for resource model
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Set store for resource model
     *
     * @param mixed $store
     * @return \Magento\Catalog\Model\Resource\Product\Flat
     */
    public function setStoreId($store)
    {
        if (is_int($store)) {
            $this->_storeId = $store;
        } else {
            $this->_storeId = (int)$this->_storeManager->getStore($store)->getId();
        }
        return $this;
    }

    /**
     * Retrieve Flat Table name
     *
     * @param mixed $store
     * @return string
     */
    public function getFlatTableName($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        return $this->getTable('catalog_product_flat_' . $store);
    }

    /**
     * Retrieve entity type id
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->_catalogConfig->getEntityType(\Magento\Catalog\Model\Product::ENTITY)
            ->getEntityTypeId();
    }

    /**
     * Retrieve attribute columns for collection select
     *
     * @param string $attributeCode
     * @return array|null
     */
    public function getAttributeForSelect($attributeCode)
    {
        $describe = $this->_getWriteAdapter()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }
        $columns = array($attributeCode => $attributeCode);

        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            $columns[$attributeIndex] = $attributeIndex;
        }

        return $columns;
    }

    /**
     * Retrieve Attribute Sort column name
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeSortColumn($attributeCode)
    {
        $describe = $this->_getWriteAdapter()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }
        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            return $attributeIndex;
        }
        return $attributeCode;
    }

    /**
     * Retrieve Flat Table columns list
     *
     * @return array
     */
    public function getAllTableColumns()
    {
        $describe = $this->_getWriteAdapter()->describeTable($this->getFlatTableName());
        return array_keys($describe);
    }

    /**
     * Check whether the attribute is a real field in entity table
     * Rewrited for EAV Collection
     *
     * @param integer|string|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return bool
     */
    public function isAttributeStatic($attribute)
    {
        $attributeCode = null;
        if ($attribute instanceof \Magento\Eav\Model\Entity\Attribute\AttributeInterface) {
            $attributeCode = $attribute->getAttributeCode();
        } elseif (is_string($attribute)) {
            $attributeCode = $attribute;
        } elseif (is_numeric($attribute)) {
            $attributeCode = $this->getAttribute($attribute)
                ->getAttributeCode();
        }

        if ($attributeCode) {
            $columns = $this->getAllTableColumns();
            if (in_array($attributeCode, $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve entity id field name in entity table
     * Rewrited for EAV collection compatible
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getIdFieldName();
    }

    /**
     * Retrieve attribute instance
     * Special for non static flat table
     *
     * @param mixed $attribute
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    public function getAttribute($attribute)
    {
        return $this->_catalogConfig->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $attribute);
    }

    /**
     * Retrieve main resource table name
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->getFlatTableName($this->getStoreId());
    }
}
