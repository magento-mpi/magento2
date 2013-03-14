<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * GoogleShopping Types collection
 *
 * @category   Mage
 * @package    Mage_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleShopping_Model_Resource_Grid_Collection extends Mage_GoogleShopping_Model_Resource_Type_Collection
{
    /**
     *  Add total count of Items for each type
     *
     * @return Mage_GoogleShopping_Model_Resource_Grid_Collection|Mage_GoogleShopping_Model_Resource_Type_Collection
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addItemsCount();
        return $this;
    }
}
