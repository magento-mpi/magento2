<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dashboard neswletter info
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Customer_Block_Account_Dashboard_Newsletter extends Magento_Core_Block_Template
{
    public function getSubscriptionObject()
    {
        if(is_null($this->_subscription)) {
            $this->_subscription = Mage::getModel('Magento_Newsletter_Model_Subscriber')
                ->loadByCustomer(Mage::getSingleton('Magento_Customer_Model_Session')->getCustomer());
        }
        return $this->_subscription;
    }
}
