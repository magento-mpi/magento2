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
    const XML_PATH_CC_TYPES = 'global/payment/cc/types';
    
    
    protected $_payment = null;
    
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
    
    public function getStore()
    {
        return $this->getData('store');
    }
    
    public function setStore($store)
    {
        $this->setData('store', $store);
        return $this;
    }
    
    /**
     * Checking availability of data for method
     *
     * @param   mixed $data
     * @return  bool
     */
    public function isValidData($data)
    {
        return true;
    }
    
    /**
     * Retrieve payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->getData('code');
    }
    
    public function getConfigData($field)
    {
        $path = 'payment/'.$this->getCode().'/'.$field;
        $store = $this->getStore();
        if ($store instanceof Mage_Core_Model_Store) {
            return $store->getConfig($path);
        }
        return Mage::getStoreConfig($path);
    }
    
    public function validateInfo(Mage_Payment_Model_Info $info)
    {
         return $this;
    }
    
    /**
     * @todo need replace this interface
     */
    public function createFormBlock($name)
    {
        return false;
    }

    public function createInfoBlock($name)
    {
        return false;
    }

    public function getLayout()
    {
        return Mage::registry('action')->getLayout();
    }

    public function getCheckoutRedirectUrl()
    {
        return false;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {
        // TODO TOFIX !!!
//        $payment->getInvoice()->setInvoiceStatusId(Mage_Sales_Model_Invoice::STATUS_PAYED);

        return $this;
    }

}
