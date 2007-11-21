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
    
    /**
     * Retrieve array of available payment methods
     *
     * @return array
     */
    public function getMethods()
    {
        $methods = $this->getData('methods');
        if (is_null($methods)) {
            $methods = $this->helper('payment')->getStoreMethods($this->getStoreId());
            foreach ($methods as $key => $method) {
            	if (!$method->canUseInternal()) {
            	    unset($methods[$key]);
            	}
            	$method->setPayment($this->getQuote()->getPayment());
            }
            $this->setData('methods', $methods);
        }
        return $methods;
    }
    
    /**
     * Check existing of payment methods
     *
     * @return bool
     */
    public function hasMethods()
    {
        $methods = $this->getMethods();
        if (is_array($methods) && count($methods)) {
            return true;
        }
        return false;
    }
    
    public function getCurrentMethod()
    {
        if ($this->getQuote()->getPayment()) {
            return $this->getQuote()->getPayment()->getMethod();
        }
        return false;
    }
}