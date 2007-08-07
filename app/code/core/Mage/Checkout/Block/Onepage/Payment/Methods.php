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
    public function fetchEnabledMethods()
    {
        $methods = Mage::getConfig()->getNode('global/sales/payment/methods')->children();
        foreach ($methods as $methodConfig) {
            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = new $className();
            $method->setPayment($payment);
            $methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            if (!empty($methodBlock)) {
                $listBlock->append($methodBlock);
            }
        }
        return $this;
    }
    
    public function getHtml()
    {
        $this->fetchEnabledMethods();
        return parent::getHtml();
    }
}