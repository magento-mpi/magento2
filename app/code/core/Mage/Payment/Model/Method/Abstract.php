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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Payment method abstract model
 */
abstract class Mage_Payment_Model_Abstract extends Varien_Object
{
    const STATUS_UNKNOWN    = 'UNKNOWN';
    const STATUS_APPROVED   = 'APPROVED';
    const STATUS_ERROR      = 'ERROR';
    const STATUS_DECLINED   = 'DECLINED';
    
    /**
     * Retrieve model helper
     *
     * @return Mage_Payment_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('payment');
    }
    
    /**
     * Retrieve payment iformation model object
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfoInstance()
    {
        $instance = $this->getData('info_instance');
        if (!($instance instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->_getHelper()->__('Can not retrieve payment iformation object instance'));
        }
        return $instance;
    }
    
    /**
     * Validate payment method information object
     * 
     * @param   Mage_Payment_Model_Info $info
     * @return  Mage_Payment_Model_Abstract
     */
    public function validate()
    {
         return $this;
    }
    
    /**
     * Authorize
     *
     * @param   Mage_Payment_Model_Info $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorise()
    {
        if (!$this->canAuthorise()) {
            Mage::throwException($this->_getHelper()->__('Authorize action is not available'));
        }
        return $this;
    }
    
    /**
     * Check authorise availability
     *
     * @return bool
     */
    public function canAuthorise()
    {
        return false;
    }
    
    /**
     * Capture payment
     *
     * @param   Mage_Payment_Model_Info $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture()
    {
        if (!$this->canCapture()) {
            Mage::throwException($this->_getHelper()->__('Capture action is not available'));
        }
        
        return $this;
    }
    
    /**
     * Check capture availability
     *
     * @return bool
     */
    public function canCapture()
    {
        return false;
    }
    
    /**
     * Refund money
     *
     * @param   Mage_Payment_Model_Info $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function refund()
    {
        if (!$this->canRefund()) {
            Mage::throwException($this->_getHelper()->__('Refund action is not available'));
        }
        
        return $this;
    }
    
    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
        return false;
    }
    
    /**
     * Void payment
     *
     * @param   Mage_Payment_Model_Info $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void()
    {
        if (!$this->canVoid()) {
            Mage::throwException($this->_getHelper()->__('Void action is not available'));
        }
        
        return $this;
    }
    
    /**
     * Check void availability
     *
     * @param   Mage_Payment_Model_Info $invoicePayment
     * @return  bool
     */
    public function canVoid(Mage_Payment_Model_Info $payment)
    {
        return true;
    }
    
    /**
     * Using internal pages for input payment data
     *
     * @return bool
     */
    public function canUseInternal()
    {
        return true;
    }
    
    /**
     * Using for multiple shipping address
     *
     * @return bool
     */
    public function canUseForMultishipping()
    {
        return true;
    }
}