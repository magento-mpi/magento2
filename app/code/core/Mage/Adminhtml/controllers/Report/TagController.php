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

    public function productAllAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/product/all')
            ->_addBreadcrumb(__('Poducts Report (Total)'), __('Products Report (Total)'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_product_all'))
            ->renderLayout();
    }

    public function popularAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/popular')
            ->_addBreadcrumb(__('Popular Tags'), __('Popular Tags'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_popular'))
            ->renderLayout();
    }

    public function customerDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/customerDetail')
            ->_addBreadcrumb(__('Customers Report'), __('Customers Report'))
            ->_addBreadcrumb(__('Customer Tags'), __('Customer Tags'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_customer_detail'))
            ->renderLayout();
    }

    public function productDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/productDetail')
            ->_addBreadcrumb(__('Products Report'), __('Products Report'))
            ->_addBreadcrumb(__('Product Tags'), __('Product Tags'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_product_detail'))
            ->renderLayout();
    }

    public function tagDetailAction()
    {
        $this->_initAction()
            ->_setActiveMenu('report/tag/tagDetail')
            ->_addBreadcrumb(__('Popular Tags'), __('Popular Tags'))
            ->_addBreadcrumb(__('Tag Detail'), __('Tag Detail'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/report_tag_popular_detail'))
            ->renderLayout();
    }

    protected function _isAllowed()
    {
	    switch ($this->getRequest()->getActionName()) {
            case 'customer':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/customer');
                break;
            case 'product':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/product');
                break;
            case 'productAll':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/product_total');
                break;
            case 'popular':
                return Mage::getSingleton('admin/session')->isAllowed('report/tags/popular');
                break;
            default:
                return Mage::getSingleton('admin/session')->isAllowed('report/tags');
                break;
        }
    }
}