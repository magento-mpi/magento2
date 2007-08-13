<?php
/**
 * Dashboard Customer Info
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Customer_Block_Account_Dashboard_Info extends Mage_Core_Block_Template
{
    public function getCustomer()
    {
        return Mage::getSingleton('customer/session')->getCustomer();
    }

    public function getChangePasswordUrl()
    {
        return Mage::getUrl('*/account/changePassword');
    }

	public function getSubscriptionObject()
    {
    	if(is_null($this->_subscription)) {
			$this->_subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer(Mage::getSingleton('customer/session')->getCustomer());
    	}

    	return $this->_subscription;
    }

    public function getIsSubscribed()
    {
    	return $this->getSubscriptionObject()->isSubscribed();
    }
}