<?php
/**
 * Order status collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Sales_Model_Entity_Order_Address_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('sales/order_address'));
        $this->setObject('sales/order_address');
    }
    
    public function setOrderFilter($orderId)
    {
        $this->addAttributeToFilter('parent_id', $orderId);
        return $this;
    }
}