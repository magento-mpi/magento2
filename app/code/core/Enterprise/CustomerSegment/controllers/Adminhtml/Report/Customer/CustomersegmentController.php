<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer Segment reports controller
 *
 * @category   Enterprise
 * @package    Enterprise_CustomerSegment
 */
class Enterprise_CustomerSegment_Adminhtml_Report_Customer_CustomersegmentController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init layout and adding breadcrumbs
     *
     * @return Enterprise_CustomerSegment_Adminhtml_Report_Customer_CustomersegmentController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('report/customers')
            ->_addBreadcrumb(Mage::helper('enterprise_customersegment')->__('Reports'),
                Mage::helper('enterprise_customersegment')->__('Reports'))
            ->_addBreadcrumb(Mage::helper('enterprise_customersegment')->__('Customers'),
                Mage::helper('enterprise_customersegment')->__('Customers'));
        return $this;
    }

    /**
     * Initialize Customer Segmen Model
     * or adding error to session storage if object was not loaded
     *
     * @param bool $outputMessage
     * @return Enterprise_CustomerSegment_Model_Segment|false
     */
    protected function _initSegment($outputMessage = true)
    {
        $segmentId = $this->getRequest()->getParam('segment_id', 0);
        /* @var $segment Enterprise_CustomerSegment_Model_Segment */
        $segment = Mage::getModel('enterprise_customersegment/segment');
        if ($segmentId) {
            $segment->load($segmentId);
        }
        if (!$segment->getId()) {
            if ($outputMessage) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('Wrong customer segment requested.'));
            }
            return false;
        }
        Mage::register('current_customer_segment', $segment);
        return $segment;
    }

    /**
     * Index Action.
     * Forward to Segment Action
     *
     */
    public function indexAction()
    {
        $this->_forward('segment');
    }

    /**
     * Segment Action
     *
     */
    public function segmentAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock(
                'enterprise_customersegment/adminhtml_report_customer_segment')
            )
            ->renderlayout();
    }

    /**
     * Detail Action of customer segment
     *
     */
    public function detailAction()
    {
        if ($this->_initSegment()) {
            $this->_initAction()
                ->_addContent($this->getLayout()->createBlock(
                    'enterprise_customersegment/adminhtml_report_customer_segment_detail'
                ))
                ->renderLayout();
        } else {
            $this->_redirect('*/*/segment');
            return ;
        }
    }

    /**
     * Apply segment conditions to all customers
     */
    public function refreshAction()
    {
        $segment = $this->_initSegment();
        if ($segment) {
            try {
                $segment->matchCustomers();
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    $this->__('Customer Segment data refreshed successfully.')
                );
                $this->_redirect('*/*/detail', array('_current' => true));
                return ;
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/detail', array('_current' => true));
        return ;
    }

    /**
     * Export Excel Action
     *
     */
    public function exportExcelAction()
    {
        if ($this->_initSegment()) {
            $fileName = 'customersegment_customers.xml';
            $content = $this->getLayout()
                ->createBlock('enterprise_customersegment/adminhtml_report_customer_segment_detail_grid')
                ->getExcel($fileName);
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return ;
        }
    }

    /**
     * Export Csv Action
     *
     */
    public function exportCsvAction()
    {
        if ($this->_initSegment()) {
            $fileName = 'customersegment_customers.csv';
            $content = $this->getLayout()
                ->createBlock('enterprise_customersegment/adminhtml_report_customer_segment_detail_grid')
                ->getCsv();
            $this->_prepareDownloadResponse($fileName, $content);
        } else {
            $this->_redirect('*/*/detail', array('_current' => true));
            return ;
        }
    }

    /**
     * Segment customer ajax grid action
     */
    public function customerGridAction()
    {
        if (!$this->_initSegment(false)) {
            return;
        }
        $grid = $this->getLayout()->createBlock('enterprise_customersegment/adminhtml_report_customer_segment_detail_grid');
        $this->getResponse()->setBody($grid->toHtml());
    }
}
