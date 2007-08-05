<?php

/**
 * Quote addresses collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Moshe Gurvich <moshe@varien.com>
 */

class Mage_Sales_Model_Entity_Quote_Address_Item_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getSingleton('sales_entity/quote_address_item'));
        $this->setObject('sales/quote_address_item');
    }
    
    public function setAddressFilter($addressId)
    {
        $this->addAttributeToFilter('parent_id', $addressId);
        return $this;
    }
}