<?php
/**
 * Customer email model
 *
 * @package    Ecom
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Model_Email extends Mage_Core_Model_Email 
{
    public function setCustomer(Mage_Customer_Model_Customer $customer)
    {
        $this->setToName($customer->getName());
        $this->setToEmail($customer->getEmail());
        $this->setTemplateVar('customer', $customer);
        return $this;
    }
}