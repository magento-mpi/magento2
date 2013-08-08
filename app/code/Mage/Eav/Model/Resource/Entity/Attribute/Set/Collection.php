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
 * Eav Resource Attribute Set Collection
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Entity_Attribute_Set', 'Mage_Eav_Model_Resource_Entity_Attribute_Set');
    }

    /**
     * Add filter by entity type id to collection
     *
     * @param int $typeId
     * @return Mage_Eav_Model_Resource_Entity_Attribute_Set_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this->addFieldToFilter('entity_type_id', $typeId);
    }

    /**
     * Convert collection items to select options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return parent::_toOptionArray('attribute_set_id', 'attribute_set_name');
    }

    /**
     * Convert collection items to select options hash array
     *
     * @return array
     */
    public function toOptionHash()
    {
        return parent::_toOptionHash('attribute_set_id', 'attribute_set_name');
    }
}
