<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Form Element Resource Model
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Form_Element extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize connection and define main table
     */
    protected function _construct()
    {
        $this->_init('eav_form_element', 'element_id');
        $this->addUniqueField(array(
            'field' => array('type_id', 'attribute_id'),
            'title' => __('Form Element with the same attribute')
        ));
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Mage_Eav_Model_Form_Element $object
     * @return Magento_DB_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);
        $select->join(
            $this->getTable('eav_attribute'),
            $this->getTable('eav_attribute') . '.attribute_id = ' . $this->getMainTable() . '.attribute_id',
            array('attribute_code', 'entity_type_id')
        );

        return $select;
    }
}
