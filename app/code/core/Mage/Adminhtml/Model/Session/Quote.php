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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
/**
 * Adminhtml quote session
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Model_Session_Quote extends Mage_Core_Model_Session_Abstract
{
    /**
     * Quote model object
     *
     * @var Mage_Sales_Model_Quote
     */
    protected $_quote   = null;
    
    /**
     * Customer mofrl object
     *
     * @var Mage_Customer_Model_Customer
     */
    protected $_customer= null;
    
    /**
     * Store model object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store   = null;
    
    public function __construct() 
    {
        $this->init('adminhtml_quote');
    }
    
    /**
     * Retrieve quote model object
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        if (is_null($this->_quote)) {
            $this->_quote = Mage::getModel('sales/quote');
            
            if ($quoteId = $this->getQuoteId()) {
                $this->_quote->load($quoteId);
            }
            elseif($this->getStoreId()) {
                $this->_quote->setStoreId($this->getStoreId())
                    ->initByCustomer($this->getCustomer())
                    ->setIsActive(false)
                    ->save();
                $this->setQuoteId($this->_quote->getId());
            }
        }
        return $this->_quote;
    }
    
    /**
     * Retrieve customer model object
     *
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer()
    {
        if (is_null($this->_customer)) {
            $this->_customer = Mage::getModel('customer/customer');
            if ($customerId = $this->getCustomerId()) {
                $this->_customer->load($customerId);
            }
        }
        return $this->_customer;
    }
    
    /**
     * Retrieve store model object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::getModel('core/store')->load($this->getStoreId());
            if ($currencyId = $this->getCurrencyId()) {
                $this->_store->setCurrentCurrencyCode($currencyId);
            }
        }
        return $this->_store;
    }
}
