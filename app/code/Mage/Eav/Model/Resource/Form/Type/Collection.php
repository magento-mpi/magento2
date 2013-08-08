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
 * Eav Form Type Resource Collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Form_Type_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Form_Type', 'Mage_Eav_Model_Resource_Form_Type');
    }

    /**
     * Convert items array to array for select options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray('type_id', 'label');
    }

    /**
     * Add Entity type filter to collection
     *
     * @param Mage_Eav_Model_Entity_Type|int $entity
     * @return Mage_Eav_Model_Resource_Form_Type_Collection
     */
    public function addEntityTypeFilter($entity)
    {
        if ($entity instanceof Mage_Eav_Model_Entity_Type) {
            $entity = $entity->getId();
        }

        $this->getSelect()
            ->join(
                array('form_type_entity' => $this->getTable('eav_form_type_entity')),
                'main_table.type_id = form_type_entity.type_id',
                array())
            ->where('form_type_entity.entity_type_id = ?', $entity);

        return $this;
    }
}
