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
 * @package    Mage_Checkout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * One page checkout status
 *
 * @category   Mage
 * @category   Mage
 * @package    Mage_Checkout
 * @author     Moshe Gurvich <moshe@varien.com>
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
            if (!$methodConfig->is('active', 1)) {
                continue;
            }

            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = Mage::getModel($className);
            if ($method) {
                $method->setPayment($this->getQuote()->getPayment());
            	$methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            	if (!empty($methodBlock)) {
	                $this->append($methodBlock);
    	        }
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