<?php
/**
 * Sales order details block
 *
 * @package    Mage
 * @subpackage Sales
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Sales_Block_Order_Details extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('sales/order/details.phtml');
        $this->setOrder(Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id')));
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(__('Order Details'));
    }

    public function getBackUrl()
    {
        return Mage::getUrl('*/*/history');
    }

    public function getInvoices()
    {
        $invoices = Mage::getResourceModel('sales/invoice_collection')->setOrderFilter($this->getOrder()->getEntityId())->load();
        return $invoices;
    }

    public function getPrintUrl()
    {
        return Mage::getUrl('*/*/print');
    }

}
