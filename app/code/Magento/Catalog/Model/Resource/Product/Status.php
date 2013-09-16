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
 * Catalog product website resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Product_Status extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Product atrribute cache
     *
     * @var array
     */
    protected $_productAttributes  = array();

    /**
     * Initialize connection
     *
     */
    protected function _construct()
    {
        $this->_init('catalog_product_enabled_index', 'product_id');
    }

    /**
     * Retrieve product attribute (public method for status model)
     *
     * @param string $attributeCode
     * @return Magento_Catalog_Model_Resource_Eav_Attribute
     */
    public function getProductAttribute($attributeCode)
    {
        return $this->_getProductAttribute($attributeCode);
    }

    /**
     * Retrieve product attribute
     *
     * @param unknown_type $attribute
     * @return Magento_Eav_Model_Entity_Attribute_Abstract
     */
    protected function _getProductAttribute($attribute)
    {
        if (empty($this->_productAttributes[$attribute])) {
            $this->_productAttributes[$attribute] =
                Mage::getSingleton('Magento_Catalog_Model_Product')->getResource()->getAttribute($attribute);
        }
        return $this->_productAttributes[$attribute];
    }

    /**
     * Refresh enabled index cache
     *
     * @param int $productId
     * @param int $storeId
     * @return Magento_Catalog_Model_Resource_Product_Status
     */
    public function refreshEnabledIndex($productId, $storeId)
    {
        if ($storeId == Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            foreach (Mage::app()->getStores() as $store) {
                $this->refreshEnabledIndex($productId, $store->getId());
            }

            return $this;
        }

        Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Product')->refreshEnabledIndex($storeId, $productId);

        return $this;
    }

    /**
     * Update product status for store
     *
     * @param int $productId
     * @param int $storId
     * @param int $value
     * @return Magento_Catalog_Model_Resource_Product_Status
     */
    public function updateProductStatus($productId, $storeId, $value)
    {
        $statusAttributeId  = $this->_getProductAttribute('status')->getId();
        $statusEntityTypeId = $this->_getProductAttribute('status')->getEntityTypeId();
        $statusTable        = $this->_getProductAttribute('status')->getBackend()->getTable();
        $refreshIndex       = true;
        $adapter            = $this->_getWriteAdapter();

        $data = new Magento_Object(array(
            'entity_type_id' => $statusEntityTypeId,
            'attribute_id'   => $statusAttributeId,
            'store_id'       => $storeId,
            'entity_id'      => $productId,
            'value'          => $value
        ));

        $data = $this->_prepareDataForTable($data, $statusTable);

        $select = $adapter->select()
            ->from($statusTable)
            ->where('attribute_id = :attribute_id')
            ->where('store_id     = :store_id')
            ->where('entity_id    = :product_id');

        $row = $adapter->fetchRow($select);

        if ($row) {
            if ($row['value'] == $value) {
                $refreshIndex = false;
            } else {
                $condition = array('value_id = ?' => $row['value_id']);
                $adapter->update($statusTable, $data, $condition);
            }
        } else {
            $adapter->insert($statusTable, $data);
        }

        if ($refreshIndex) {
            $this->refreshEnabledIndex($productId, $storeId);
        }

        return $this;
    }

    /**
     * Retrieve Product(s) status for store
     * Return array where key is a product_id, value - status
     *
     * @param array|int $productIds
     * @param int $storeId
     * @return array
     */
    public function getProductStatus($productIds, $storeId = null)
    {
        $statuses = array();

        $attribute      = $this->_getProductAttribute('status');
        $attributeTable = $attribute->getBackend()->getTable();
        $adapter        = $this->_getReadAdapter();

        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }

        if ($storeId === null || $storeId == Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID) {
            $select = $adapter->select()
                ->from($attributeTable, array('entity_id', 'value'))
                ->where('entity_id IN (?)', $productIds)
                ->where('attribute_id = ?', $attribute->getAttributeId())
                ->where('store_id = ?', Magento_Catalog_Model_Abstract::DEFAULT_STORE_ID);

            $rows = $adapter->fetchPairs($select);
        } else {
            $valueCheckSql = $adapter->getCheckSql('t2.value_id > 0', 't2.value', 't1.value');

            $select = $adapter->select()
                ->from(
                    array('t1' => $attributeTable),
                    array('value' => $valueCheckSql))
                ->joinLeft(
                    array('t2' => $attributeTable),
                    't1.entity_id = t2.entity_id AND t1.attribute_id = t2.attribute_id AND t2.store_id = '
                        . (int)$storeId,
                    array('t1.entity_id')
                )
                ->where('t1.store_id = ?', Magento_Core_Model_AppInterface::ADMIN_STORE_ID)
                ->where('t1.attribute_id = ?', $attribute->getAttributeId())
                ->where('t1.entity_id IN(?)', $productIds);
            $rows = $adapter->fetchPairs($select);
        }

        foreach ($productIds as $productId) {
            if (isset($rows[$productId])) {
                $statuses[$productId] = $rows[$productId];
            } else {
                $statuses[$productId] = -1;
            }
        }

        return $statuses;
    }
}
