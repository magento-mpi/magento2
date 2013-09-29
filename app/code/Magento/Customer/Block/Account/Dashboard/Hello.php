<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Customer\Block\Account\Dashboard;

class Hello extends \Magento\Core\Block\Template
{

    public function getCustomerName()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getName();
    }

}
