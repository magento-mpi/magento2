<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Types collection
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Model_Resource_Grid_Collection extends Magento_GoogleShopping_Model_Resource_Type_Collection
{
    /**
     *  Add total count of Items for each type
     *
     * @return Magento_GoogleShopping_Model_Resource_Grid_Collection|Magento_GoogleShopping_Model_Resource_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addItemsCount();
        return $this;
    }
}
