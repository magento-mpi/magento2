<?php
/**
 * Sales order view block
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Sales_Block_Order_View extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/view.phtml');
        $this->setOrder(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(__('Order Invoices'));
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
                $method->setPayment($this->getOrder()->getPayment());
            	$methodBlock = $method->createInfoBlock('payment.method.'.$methodName);
            	if (!empty($methodBlock)) {
            	    $html = $methodBlock->toHtml();
    	        }
            }
        }
        return $html;
    }

}
