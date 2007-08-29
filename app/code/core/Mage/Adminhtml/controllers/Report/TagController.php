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
 * Tag report admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
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