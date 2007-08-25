<?php
/**
 * sales admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */
class Mage_Adminhtml_ReportController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_addBreadcrumb(__('Reports'), __('Reports'));
        return $this;
    }

    public function salesAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/sales')
            ->_addBreadcrumb(__('Sales Report'), __('Sales Report'))
            ->renderLayout();
    }

    public function shopcartAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/shopcart')
            ->_addBreadcrumb(__('Shopping Cart Report'), __('Shopping Cart Report'))
            ->renderLayout();
    }

    public function productsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/product')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_product'))
            ->renderLayout();
    }

    public function couponsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/coupons')
            ->_addBreadcrumb(__('Coupons Reports'), __('Coupons Reports'))
            ->renderLayout();
    }

    public function wishlistAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/wishlist')
            ->_addBreadcrumb(__('Wishlist Report'), __('Wishlist Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_wishlist'))
            ->renderLayout();
    }

    public function searchAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/search')
            ->_addBreadcrumb(__('Search Report'), __('Search Report'))
            ->renderLayout();
    }

    public function customersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/customers')
            ->_addBreadcrumb(__('Best Customers'), __('Best Customers'))
            ->renderLayout();
    }

    public function ordersAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/orders')
            ->_addBreadcrumb(__('Recent Orders'), __('Recent Orders'))
            ->renderLayout();
    }

    public function totalsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/totals')
            ->_addBreadcrumb(__('Order Totals'), __('Order Totals'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
	    switch ($this->getRequest()->getActionName()) {
            case 'sales':
                return Mage::getSingleton('admin/session')->isAllowed('report/sales');
                break;
            case 'shopcart':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
            case 'products':
                return Mage::getSingleton('admin/session')->isAllowed('report/products');
                break;
            case 'coupons':
                return Mage::getSingleton('admin/session')->isAllowed('report/coupons');
                break;
            case 'wishlist':
                return Mage::getSingleton('admin/session')->isAllowed('report/wishlist');
                break;
            case 'search':
                return Mage::getSingleton('admin/session')->isAllowed('report/search');
                break;
            case 'customers':
                return Mage::getSingleton('admin/session')->isAllowed('report/shopcart');
                break;
            case 'orders':
                return Mage::getSingleton('admin/session')->isAllowed('report/orders');
                break;
            case 'totals':
                return Mage::getSingleton('admin/session')->isAllowed('report/totals');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report');
                break;
        }
    }
}