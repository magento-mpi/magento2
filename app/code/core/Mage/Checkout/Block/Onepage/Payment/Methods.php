<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_Payment_Methods extends Mage_Core_Block_Text_List
{
    public function getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }
    
    public function fetchEnabledMethods()
    {
        $methods = Mage::getStoreConfig('payment');

        foreach ($methods as $methodConfig) {
            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = Mage::getModel($className)
                ->setPayment($this->getQuote()->getPayment());
            $methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            if (!empty($methodBlock)) {
                $this->append($methodBlock);
            }
        }
        return $this;
    }
    
    public function toHtml()
    {
        $this->fetchEnabledMethods();
        return parent::toHtml();
    }
}