<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Customer_Block_Account_Dashboard_Hello extends Magento_Core_Block_Template
{

    public function getCustomerName()
    {
        return Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer()->getName();
    }

}
