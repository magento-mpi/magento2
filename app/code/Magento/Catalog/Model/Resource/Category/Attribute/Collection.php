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
 * Catalog category EAV additional attribute resource collection
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Resource_Category_Attribute_Collection
    extends Magento_Eav_Model_Resource_Entity_Attribute_Collection
{
    /**
     * Main select object initialization.
     * Joins catalog/eav_attribute table
     *
     * @return Magento_Catalog_Model_Resource_Category_Attribute_Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where('main_table.entity_type_id=?', Mage::getModel('Magento_Eav_Model_Entity')->setType(Magento_Catalog_Model_Category::ENTITY)->getTypeId())
            ->join(
                array('additional_table' => $this->getTable('catalog_eav_attribute')),
                'additional_table.attribute_id = main_table.attribute_id'
            );
        return $this;
    }

    /**
     * Specify attribute entity type filter
     *
     * @param int $typeId
     * @return Magento_Catalog_Model_Resource_Category_Attribute_Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
