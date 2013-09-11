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
namespace Magento\Catalog\Model\Resource\Category\Attribute;

class Collection
    extends \Magento\Eav\Model\Resource\Entity\Attribute\Collection
{
    /**
     * Main select object initialization.
     * Joins catalog/eav_attribute table
     *
     * @return \Magento\Catalog\Model\Resource\Category\Attribute\Collection
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(array('main_table' => $this->getResource()->getMainTable()))
            ->where('main_table.entity_type_id=?', \Mage::getModel('\Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Category::ENTITY)->getTypeId())
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
     * @return \Magento\Catalog\Model\Resource\Category\Attribute\Collection
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }
}
