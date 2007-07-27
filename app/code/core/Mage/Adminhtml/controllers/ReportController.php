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
            ->_setActiveMenu('report/products')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
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
            ->renderLayout();
    }

    public function reviewsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/reviews')
            ->_addBreadcrumb(__('Reviews Report'), __('Reviews Report'))
            ->renderLayout();
    }

    public function tagsAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tags')
            ->_addBreadcrumb(__('Tags Report'), __('Tags Report'))
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

}