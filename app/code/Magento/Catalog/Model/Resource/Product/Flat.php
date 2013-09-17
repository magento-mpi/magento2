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
class Magento_Catalog_Model_Resource_Product_Flat extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Store scope Id
     *
     * @var int
     */
    protected $_storeId;

    /**
     * Init connection and resource table
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_flat', 'entity_id');
        $this->_storeId = (int)Mage::app()->getStore()->getId();
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
     * @return Magento_Catalog_Model_Resource_Product_Flat
     */
    public function setStoreId($store)
    {
        if (is_int($store)) {
            $this->_storeId = $store;
        } else {
            $this->_storeId = (int)Mage::app()->getStore($store)->getId();
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
        return Mage::getSingleton('Magento_Catalog_Model_Config')
            ->getEntityType(Magento_Catalog_Model_Product::ENTITY)
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
     * @param integer|string|Magento_Eav_Model_Entity_Attribute_Abstract $attribute
     * @return bool
     */
    public function isAttributeStatic($attribute)
    {
        $attributeCode = null;
        if ($attribute instanceof Magento_Eav_Model_Entity_Attribute_Interface) {
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
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     */
    public function getAttribute($attribute)
    {
        return Mage::getSingleton('Magento_Catalog_Model_Config')
            ->getAttribute(Magento_Catalog_Model_Product::ENTITY, $attribute);
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
