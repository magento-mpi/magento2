<?php
/**
 * Adminhtml sales order create payment method form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Billing_Method_Form extends Mage_Adminhtml_Block_Widget
{

    protected $_rates;

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

    public function getStoreId()
    {
        return $this->getParentBlock()->getStoreId();
    }

    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }

    public function fetchEnabledMethods()
    {
        $methods = Mage::getStoreConfig('payment', $this->getStoreId());

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
            $payment->setCcNumber(null)->setCcCid(null);
        }
        return $payment;
    }

    public function getInnerHtml()
    {
        $this->fetchEnabledMethods();
        return $this->_innerHtml;
    }


}
