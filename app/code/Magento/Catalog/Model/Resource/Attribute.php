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
 * Catalog attribute resource model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Attribute extends Magento_Eav_Model_Resource_Entity_Attribute
{
    /**
     * Perform actions before object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Attribute
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        $applyTo = $object->getApplyTo();
        if (is_array($applyTo)) {
            $object->setApplyTo(implode(',', $applyTo));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param  Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Attribute
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        $this->_clearUselessAttributeValues($object);
        return parent::_afterSave($object);
    }

    /**
     * Clear useless attribute values
     *
     * @param  Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Attribute
     */
    protected function _clearUselessAttributeValues(Magento_Core_Model_Abstract $object)
    {
        $origData = $object->getOrigData();

        if ($object->isScopeGlobal()
            && isset($origData['is_global'])
            && Magento_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL != $origData['is_global']
        ) {
            $attributeStoreIds = array_keys(Mage::app()->getStores());
            if (!empty($attributeStoreIds)) {
                $delCondition = array(
                    'entity_type_id=?' => $object->getEntityTypeId(),
                    'attribute_id = ?' => $object->getId(),
                    'store_id IN(?)'   => $attributeStoreIds
                );
                $this->_getWriteAdapter()->delete($object->getBackendTable(), $delCondition);
            }
        }

        return $this;
    }

    /**
     * Delete entity
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Catalog_Model_Resource_Attribute
     */
    public function deleteEntity(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getEntityAttributeId()) {
            return $this;
        }

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav_entity_attribute'))
            ->where('entity_attribute_id = ?', (int)$object->getEntityAttributeId());
        $result = $this->_getReadAdapter()->fetchRow($select);

        if ($result) {
            $attribute = Mage::getSingleton('Magento_Eav_Model_Config')
                ->getAttribute(Magento_Catalog_Model_Product::ENTITY, $result['attribute_id']);

            if ($this->isUsedBySuperProducts($attribute, $result['attribute_set_id'])) {
                Mage::throwException(Mage::helper('Magento_Eav_Helper_Data')->__("Attribute '%s' used in configurable products", $attribute->getAttributeCode()));
            }
            $backendTable = $attribute->getBackend()->getTable();
            if ($backendTable) {
                $select = $this->_getWriteAdapter()->select()
                    ->from($attribute->getEntity()->getEntityTable(), 'entity_id')
                    ->where('attribute_set_id = ?', $result['attribute_set_id']);

                $clearCondition = array(
                    'entity_type_id =?' => $attribute->getEntityTypeId(),
                    'attribute_id =?'   => $attribute->getId(),
                    'entity_id IN (?)'  => $select
                );
                $this->_getWriteAdapter()->delete($backendTable, $clearCondition);
            }
        }

        $condition = array('entity_attribute_id = ?' => $object->getEntityAttributeId());
        $this->_getWriteAdapter()->delete($this->getTable('eav_entity_attribute'), $condition);

        return $this;
    }

    /**
     * Defines is Attribute used by super products
     *
     * @param Magento_Core_Model_Abstract $object
     * @param int $attributeSet
     * @return int
     */
    public function isUsedBySuperProducts(Magento_Core_Model_Abstract $object, $attributeSet = null)
    {
        $adapter      = $this->_getReadAdapter();
        $attrTable    = $this->getTable('catalog_product_super_attribute');
        $productTable = $this->getTable('catalog_product_entity');

        $bind = array('attribute_id' => $object->getAttributeId());
        $select = clone $adapter->select();
        $select->reset()
            ->from(array('main_table' => $attrTable), array('psa_count' => 'COUNT(product_super_attribute_id)'))
            ->join(array('entity' => $productTable), 'main_table.product_id = entity.entity_id')
            ->where('main_table.attribute_id = :attribute_id')
            ->group('main_table.attribute_id')
            ->limit(1);

        if ($attributeSet !== null) {
            $bind['attribute_set_id'] = $attributeSet;
            $select->where('entity.attribute_set_id = :attribute_set_id');
        }

        return $adapter->fetchOne($select, $bind);
    }
}
