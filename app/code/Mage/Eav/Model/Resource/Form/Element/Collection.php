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
 * Eav Form Element Resource Collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Form_Element_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Form_Element', 'Mage_Eav_Model_Resource_Form_Element');
    }

    /**
     * Add Form Type filter to collection
     *
     * @param Mage_Eav_Model_Form_Type|int $type
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    public function addTypeFilter($type)
    {
        if ($type instanceof Mage_Eav_Model_Form_Type) {
            $type = $type->getId();
        }

        return $this->addFieldToFilter('type_id', $type);
    }

    /**
     * Add Form Fieldset filter to collection
     *
     * @param Mage_Eav_Model_Form_Fieldset|int $fieldset
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    public function addFieldsetFilter($fieldset)
    {
        if ($fieldset instanceof Mage_Eav_Model_Form_Fieldset) {
            $fieldset = $fieldset->getId();
        }

        return $this->addFieldToFilter('fieldset_id', $fieldset);
    }

    /**
     * Add Attribute filter to collection
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract|int $attribute
     *
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    public function addAttributeFilter($attribute)
    {
        if ($attribute instanceof Mage_Eav_Model_Entity_Attribute_Abstract) {
            $attribute = $attribute->getId();
        }

        return $this->addFieldToFilter('attribute_id', $attribute);
    }

    /**
     * Set order by element sort order
     *
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    public function setSortOrder()
    {
        $this->setOrder('sort_order', self::SORT_ORDER_ASC);

        return $this;
    }

    /**
     * Join attribute data
     *
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    protected function _joinAttributeData()
    {
        $this->getSelect()->join(
            array('eav_attribute' => $this->getTable('eav_attribute')),
            'main_table.attribute_id = eav_attribute.attribute_id',
            array('attribute_code', 'entity_type_id')
        );

        return $this;
    }

    /**
     * Load data (join attribute data)
     *
     * @param boolean $printQuery
     * @param boolean $logQuery
     * @return Mage_Eav_Model_Resource_Form_Element_Collection
     */
    public function load($printQuery = false, $logQuery = false)
    {
        if (!$this->isLoaded()) {
            $this->_joinAttributeData();
        }
        return parent::load($printQuery, $logQuery);
    }
}
