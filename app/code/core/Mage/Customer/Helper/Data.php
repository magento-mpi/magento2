<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Customer data helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isLoggedIn()
    {
        return Mage::getSingleton('customer/session')->isLoggedIn();
    }
    
    /**
     * Get logged in customer
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (empty($this->_customer)) {
            $this->_customer = Mage::getSingleton('customer/session')->getCustomer();
        }
        return $this->_customer;
    }
        
    public function getCurrentCustomer()
    {
        return $this->getCustomer();
    }
    
    public function getCustomerName()
    {
        return $this->getCustomer()->getName();
    }

    public function customerHasAddresses()
    {
        return $this->getCustomer()->getLoadedAddressCollection()->count()>0;
    }
    
    public function getCustomerAddress()
    {
        
    }
    
    public function getLoginUrl()
    {
        return Mage::getUrl('customer/account/login');
    }

    public function getLogoutUrl()
    {
        return Mage::getUrl('customer/account/logout');
    }
    
    public function getAccountUrl()
    {
        return Mage::getUrl('customer/account');
    }
}
