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
 * @package    Mage_Checkout
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_Block_Onepage_Progress extends Mage_Checkout_Block_Onepage_Abstract
{
    public function getBilling()
    {
        return $this->getQuote()->getBillingAddress();
    }
    
    public function getShipping()
    {
        return $this->getQuote()->getShippingAddress();
    }
    
    public function getShippingMethod()
    {
        return $this->getQuote()->getShippingAddress()->getShippingMethod();
    }
        
    public function getShippingDescription()
    {
        return $this->getQuote()->getShippingAddress()->getShippingDescription();
    }
    
    public function getShippingAmount()
    {
        $amount = $this->getQuote()->getShippingAddress()->getShippingAmount();
        $filter = Mage::getSingleton('core/store')->getPriceFilter();
        return $filter->filter($amount);
    }
    
    public function getPaymentHtml()
    {
        $payment = $this->getQuote()->getPayment();
        
        $html = '<p>'.Mage::getStoreConfig('payment/'.$payment->getMethod().'/title').'</p>';

        $model = Mage::getStoreConfig('payment/'.$payment->getMethod().'/model');
        $block = Mage::getModel($model)
            ->setPayment($payment)
            ->createInfoBlock($this->getName().'.payment');
        
        $html.= $block->toHtml();
        
        return $html;
    }
}