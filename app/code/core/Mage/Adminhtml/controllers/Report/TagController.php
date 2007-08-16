<?php
/**
 * Tag report admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */
class Mage_Adminhtml_Report_TagController extends Mage_Adminhtml_Controller_Action
{
    public function _initAction()
    {
        $this->loadLayout('baseframe')
            ->_addBreadcrumb(__('Reports'), __('Reports'))
            ->_addBreadcrumb(__('Tag'), __('Tag'));
        return $this;
    }

    public function customerAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/customer')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_customer'))
            ->renderLayout();
    }

    public function productAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/product')
            ->_addBreadcrumb(__('Poducts Report'), __('Products Report'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_product'))
            ->renderLayout();
    }
/*
    public function productDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/review/productDetail')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addBreadcrumb(__('Product Reviews'), __('Product Reviews'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_review_detail'))
            ->renderLayout();
    }
 */
}