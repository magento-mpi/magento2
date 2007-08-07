<?php
/**
 * Invoices collection
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @author     Moshe Gurvich <moshe@varien.com>
 */

class Mage_Sales_Model_Entity_Invoice_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    public function __construct()
    {
        $this->setEntity(Mage::getResourceSingleton('sales/invoice'));
        $this->setObject('sales/invoice');
    }

    public function setOrderFilter($orderId)
    {
        $this->addAttributeToFilter('order_id', $orderId);
        return $this;
    }

}
