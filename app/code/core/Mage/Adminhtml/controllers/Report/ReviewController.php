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
class Mage_Adminhtml_Report_ReviewController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Review'), __('Reviews'));
        return $this;
    }

    public function customerAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/customer')
            ->_addBreadcrumb(__('Sales Report'), __('Sales Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_customer'))
            ->renderLayout();
    }

    public function productAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/product')
            ->_addBreadcrumb(__('Shopping Cart Report'), __('Shopping Cart Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_product'))
            ->renderLayout();
    }

    public function productDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/productDetail')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addBreadcrumb(__('Product Reviews'), __('Product Reviews'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_detail'))
            ->renderLayout();
    }

}