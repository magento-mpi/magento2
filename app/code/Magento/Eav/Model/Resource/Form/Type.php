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
 * Eav Form Type Resource Model
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Resource_Form_Type extends Magento_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     *
     */
    protected function _construct()
    {
        $this->_init('eav_form_type', 'type_id');
        $this->addUniqueField(array(
            'field' => array('code', 'theme', 'store_id'),
            'title' => __('Form Type with the same code')
        ));
    }

    /**
     * Load an object
     *
     * @param Magento_Eav_Model_Form_Type $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return Magento_Eav_Model_Resource_Form_Type
     */
    public function load(Magento_Core_Model_Abstract $object, $value, $field = null)
    {
        if (is_null($field) && !is_numeric($value)) {
            $field = 'code';
        }
        return parent::load($object, $value, $field);
    }

    /**
     * Retrieve form type entity types
     *
     * @param Magento_Eav_Model_Form_Type $object
     * @return array
     */
    public function getEntityTypes($object)
    {
        $objectId = $object->getId();
        if (!$objectId) {
            return array();
        }
        $adapter = $this->_getReadAdapter();
        $bind    = array(':type_id' => $objectId);
        $select  = $adapter->select()
            ->from($this->getTable('eav_form_type_entity'), 'entity_type_id')
            ->where('type_id = :type_id');

        return $adapter->fetchCol($select, $bind);
    }

    /**
     * Save entity types after save form type
     *
     * @see Magento_Core_Model_Resource_Db_Abstract#_afterSave($object)
     *
     * @param Magento_Eav_Model_Form_Type $object
     * @return Magento_Eav_Model_Resource_Form_Type
     */
    protected function _afterSave(Magento_Core_Model_Abstract $object)
    {
        if ($object->hasEntityTypes()) {
            $new = $object->getEntityTypes();
            $old = $this->getEntityTypes($object);

            $insert = array_diff($new, $old);
            $delete = array_diff($old, $new);

            $adapter  = $this->_getWriteAdapter();

            if (!empty($insert)) {
                $data = array();
                foreach ($insert as $entityId) {
                    if (empty($entityId)) {
                        continue;
                    }
                    $data[] = array(
                        'entity_type_id' => (int)$entityId,
                        'type_id'        => $object->getId()
                    );
                }
                if ($data) {
                    $adapter->insertMultiple($this->getTable('eav_form_type_entity'), $data);
                }
            }

            if (!empty($delete)) {
                $where = array(
                    'entity_type_id IN (?)' => $delete,
                    'type_id = ?'           => $object->getId()
                );
                $adapter->delete($this->getTable('eav_form_type_entity'), $where);
            }
        }

        return parent::_afterSave($object);
    }

    /**
     * Retrieve form type filtered by given attribute
     *
     * @param Magento_Eav_Model_Entity_Attribute_Abstract|int $attribute
     * @return array
     */
    public function getFormTypesByAttribute($attribute)
    {
        if ($attribute instanceof Magento_Eav_Model_Entity_Attribute_Abstract) {
            $attribute = $attribute->getId();
        }
        if (!$attribute) {
            return array();
        }
        $bind   = array(':attribute_id' => $attribute);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('eav_form_element'))
            ->where('attribute_id = :attribute_id');

        return $this->_getReadAdapter()->fetchAll($select, $bind);
    }
}
