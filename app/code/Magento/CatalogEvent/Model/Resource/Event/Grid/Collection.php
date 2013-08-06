<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Event resource collection
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogEvent_Model_Resource_Event_Grid_Collection
    extends Magento_CatalogEvent_Model_Resource_Event_Collection
{
    /**
     * Add category data to collection select (name, position)
     *
     * @return Magento_CatalogEvent_Model_Resource_Event_Collection|Magento_CatalogEvent_Model_Resource_Event_Grid_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addCategoryData();
        return $this;
    }
}

