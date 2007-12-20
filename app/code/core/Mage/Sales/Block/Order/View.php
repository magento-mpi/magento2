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
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales order view block
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Sales_Block_Order_View extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/view.phtml');

        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(Mage::helper('sales')->__('Order Invoices'));
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        $methodName = $this->getOrder()->getPayment()->getMethod();
        $html = '';
        $methodConfig = new Varien_Object(Mage::getStoreConfig('payment/' . $this->getOrder()->getPayment()->getMethod(), $this->getOrder()->getStoreId()));
        if ($methodConfig) {
            $className = $methodConfig->getModel();
            $method = Mage::getModel($className);
            if ($method) {
                $html = '<p>'.Mage::getStoreConfig('payment/' . $this->getOrder()->getPayment()->getMethod() . '/title').'</p>';
                $method->setPayment($this->getOrder()->getPayment());
            	$methodBlock = $method->createInfoBlock('payment.method.'.$methodName);
            	if (!empty($methodBlock)) {
            	    $html .= $methodBlock->toHtml();
    	        }
            }
        }
        return $html;
    }

    public function getOrder()
    {
        if (!$this->getData('order')) {
            $orderId = $this->getRequest()->getParam('order_id');
            $this->setOrder(Mage::getModel('sales/order')->load($orderId));
        }
        return $this->getData('order');
    }
}
