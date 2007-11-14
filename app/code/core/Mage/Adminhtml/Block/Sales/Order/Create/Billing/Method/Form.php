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
 * Adminhtml sales order create payment method form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Michael Bessolov <michael@varien.com>
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form extends Mage_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected $_innerHtml = '';

    public function __construct()
    {
        parent::__construct();
        $this->setId('sales_order_create_billing_method_form');
        $this->setTemplate('sales/order/create/billing/method/form.phtml');
    }

    public function getAddress()
    {
        return $this->getParentBlock()->getSession()->getBillingAddress();
    }

    public function fetchEnabledMethods()
    {
        $methods = $this->helper('payment')->getStoreMethods($this->getStoreId());
        
        foreach ($methods as $methodConfig) {
            $methodName = $methodConfig->getName();
            $className = $methodConfig->getClassName();
            $method = Mage::getModel($className);
            if ($method) {
                $method->setPayment($this->getQuote()->getPayment());
            	$methodBlock = $method->createFormBlock('checkout.payment.methods.'.$methodName);
            	if (!empty($methodBlock)) {
            	    $this->_innerHtml .= $methodBlock->toHtml();
    	        }
            }
        }
        return $this;
    }

    public function getPayment()
    {
        $payment = $this->getQuote()->getPayment();
        if (empty($payment)) {
            $payment = Mage::getModel('sales/quote_payment');
        } else {
            $payment->setCcNumber(null)
                ->setCcCid(null);
        }
        return $payment;
    }

    public function getInnerHtml()
    {
        $this->fetchEnabledMethods();
        return $this->_innerHtml;
    }


}
