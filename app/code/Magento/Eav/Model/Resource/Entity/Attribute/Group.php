<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Eav Resource Entity Attribute Group
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Resource_Entity_Attribute_Group extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Constants for attribute group codes
     */
    const TAB_GENERAL_CODE = 'product-details';
    const TAB_IMAGE_MANAGEMENT_CODE = 'image-management';

    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav_attribute_group', 'attribute_group_id');
    }

    /**
     * Checks if attribute group exists
     *
     * @param Magento_Eav_Model_Entity_Attribute_Group $object
     * @return boolean
     */
    public function itemExists($object)
    {
        $adapter   = $this->_getReadAdapter();
        $bind      = array(
            'attribute_set_id'      => $object->getAttributeSetId(),
            'attribute_group_name'  => $object->getAttributeGroupName()
        );
        $select = $adapter->select()
            ->from($this->getMainTable())
            ->where('attribute_set_id = :attribute_set_id')
            ->where('attribute_group_name = :attribute_group_name');

        return $adapter->fetchRow($select, $bind) > 0;
    }

    /**
     * Perform actions before object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _beforeSave(Magento_Core_Model_Abstract $object)
    {
        if (!$object->getSortOrder()) {
            $object->setSortOrder($this->_getMaxSortOrder($object) + 1);
        }
        return parent::_beforeSave($object);
    }

    /**
     * Perform actions after object save
     *
     * @param Magento_Core_Model_Abstract $object
     * @return Magento_Core_Model_Resource_Db_Abstract
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if ($object->getAttributes()) {
            foreach ($object->getAttributes() as $attribute) {
                $attribute->setAttributeGroupId($object->getId());
                $attribute->save();
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retreive max sort order
     *
     * @param Magento_Core_Model_Abstract $object
     * @return int
     */
    protected function _getMaxSortOrder($object)
    {
        $adapter = $this->_getReadAdapter();
        $bind    = array(':attribute_set_id' => $object->getAttributeSetId());
        $select  = $adapter->select()
            ->from($this->getMainTable(), new Zend_Db_Expr("MAX(sort_order)"))
            ->where('attribute_set_id = :attribute_set_id');

        return $adapter->fetchOne($select, $bind);
    }

    /**
     * Set any group default if old one was removed
     *
     * @param integer $attributeSetId
     * @return Magento_Eav_Model_Resource_Entity_Attribute_Group
     */
    public function updateDefaultGroup($attributeSetId)
    {
        $adapter = $this->_getWriteAdapter();
        $bind    = array(':attribute_set_id' => $attributeSetId);
        $select  = $adapter->select()
            ->from($this->getMainTable(), $this->getIdFieldName())
            ->where('attribute_set_id = :attribute_set_id')
            ->order('default_id ' . \Magento\Data\Collection::SORT_ORDER_DESC)
            ->limit(1);

        $groupId = $adapter->fetchOne($select, $bind);

        if ($groupId) {
            $data  = array('default_id' => 1);
            $where = array('attribute_group_id =?' => $groupId);
            $adapter->update($this->getMainTable(), $data, $where);
        }

        return $this;
    }
}
