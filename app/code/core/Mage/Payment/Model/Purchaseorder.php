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


class Mage_Payment_Model_PurchaseOrder extends Mage_Payment_Model_Abstract 
{
    public function createFormBlock($name)
    {        
        $block = $this->getLayout()->createBlock('payment/form', $name)
            ->setMethod('purchaseorder')
            ->setPayment($this->getPayment())
            ->setTemplate('payment/form/purchaseorder.phtml');
        
        return $block;
    }
    
    public function processFormPost($post)
    {
        
    }
    
    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info', $name)
            ->setPayment($this->getPayment())
            ->setTemplate('payment/info/purchaseorder.phtml');
        
        return $block;
    }
    
    
    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $payment->setStatus('APPROVED');
        $payment->getOrder()->addStatus(Mage::getStoreConfig('payment/purchaseorder/order_status'));
        return $this;
    }
}