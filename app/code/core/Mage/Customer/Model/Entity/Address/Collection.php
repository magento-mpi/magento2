<?php
/**
 * Customers collection
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Entity_Address_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('customer/address'));
        $this->setObject('customer/address');
    }
    
    public function setCustomerFilter($customer)
    {
        if ($customer->getId()) {
            $this->addAttributeToFilter('parent_id', $customer->getId());
        }
        else {
            $this->addAttributeToFilter('parent_id', '-1');
        }
        return $this;
    }
}