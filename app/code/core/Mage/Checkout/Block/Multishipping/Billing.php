<?php
/**
 * Multishipping billing information
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Multishipping_Billing extends Mage_Checkout_Block_Multishipping_Abstract
{
    public function getAddress()
    {
        $address = $this->getData('address');
        if (is_null($address)) {
            $address = $this->getCheckout()->getQuote()->getBillingAddress();
            $this->setAddress('address', $address);
        }
        return $address;
    }
    
    public function getPaymentMethods()
    {
        $methods = Mage::getStoreConfig('payment');
        
        $listBlock = $this->getLayout()->createBlock('core/text_list');
        foreach ($methods as $methodConfig) {
            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = Mage::getModel($className)
                ->setPayment($this->getCheckout()->getQuote()->getPayment());
                
            $methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            if (!empty($methodBlock)) {
                $listBlock->append($methodBlock);
            }
        }
        return $listBlock->toHtml();
    }
    
    public function getSelectAddressUrl()
    {
        return $this->getUrl('*/multishipping_address/selectBilling');
    }
    
    public function getPostActionUrl()
    {
        return $this->getUrl('*/*/billingPost');
    }
    
    public function getBackUrl()
    {
        return $this->getUrl('*/*/shipping');
    }
}
