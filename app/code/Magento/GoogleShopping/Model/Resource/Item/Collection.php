<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Content items collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Resource_Item_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_GoogleShopping_Model_Item', 'Magento_GoogleShopping_Model_Resource_Item');
    }

    /**
     * Init collection select
     *
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinTables();
        return $this;
    }

    /**
     * Filter collection by specified store ids
     *
     * @param array|int $storeIds
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    public function addStoreFilter($storeIds)
    {
        $this->getSelect()->where('main_table.store_id IN (?)', $storeIds);
        return $this;
    }

    /**
     * Filter collection by specified product id
     *
     * @param int $productId
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    public function addProductFilterId($productId)
    {
        $this->getSelect()->where('main_table.product_id=?', $productId);
        return $this;
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     * @param string $field
     * @param null|string|array $condition
     * @return Magento_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition=null)
    {
        if ($field == 'name') {
            $conditionSql = $this->_getConditionSql(
                $this->getConnection()->getIfNullSql('p.value', 'p_d.value'), $condition
            );
            $this->getSelect()->where($conditionSql, null, \Magento\DB\Select::TYPE_CONDITION);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
    }

    /**
     * Join product and type data
     *
     * @return Magento_GoogleShopping_Model_Resource_Item_Collection
     */
    protected function _joinTables()
    {
        $entityType = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('catalog_product');
        $attribute = Mage::getSingleton('Magento_Eav_Model_Config')->getAttribute($entityType->getEntityTypeId(),'name');

        $joinConditionDefault =
            sprintf("p_d.attribute_id=%d AND p_d.store_id='0' AND main_table.product_id=p_d.entity_id",
                $attribute->getAttributeId()
            );
        $joinCondition =
            sprintf("p.attribute_id=%d AND p.store_id=main_table.store_id AND main_table.product_id=p.entity_id",
                $attribute->getAttributeId()
            );

        $this->getSelect()
            ->joinLeft(
                array('p_d' => $attribute->getBackend()->getTable()),
                $joinConditionDefault,
                array());

        $this->getSelect()
            ->joinLeft(
                array('p' => $attribute->getBackend()->getTable()),
                $joinCondition,
                array('name' => $this->getConnection()->getIfNullSql('p.value', 'p_d.value')));

        $this->getSelect()
            ->joinLeft(
                array('types' => $this->getTable('googleshopping_types')),
                'main_table.type_id=types.type_id'
            );
        Mage::getResourceHelper('Magento_Core')->prepareColumnsList($this->getSelect()); // avoid column name collision

        return $this;
    }
}
