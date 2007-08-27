<?php
/**
 * Shopping Cart reports admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dmitriy@varien.com>
 */
class Mage_Adminhtml_Report_ShopcartController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Shopping Cart'), __('Shopping Cart'));
        return $this;
    }

    public function customerAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/shopcart/customer')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_shopcart_customer'))
            ->renderLayout();
    }

    public function productAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/shopcart/product')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_shopcart_product'))
            ->renderLayout();
    }
    
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/customer');
                break;
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart/product');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
        }
    }
}