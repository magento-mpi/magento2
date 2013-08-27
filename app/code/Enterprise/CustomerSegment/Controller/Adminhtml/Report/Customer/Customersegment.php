<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment reports controller
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Controller_Adminhtml_Report_Customer_Customersegment
    extends Magento_Adminhtml_Controller_Action
{
    /**
     * Admin session
     *
     * @var Magento_Backend_Model_Auth_Session
     */
    protected $_adminSession = null;

    /**
     * Init layout and adding breadcrumbs
     *
     * @return Enterprise_CustomerSegment_Controller_Adminhtml_Report_Customer_Customersegment
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('Enterprise_CustomerSegment::report_customers_segment')
            ->_addBreadcrumb(
                __('Reports'),
                __('Reports')
            )
            ->_addBreadcrumb(
                __('Customers'),
                __('Customers')
            );
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
        $segmentIds = $this->getRequest()->getParam('massaction');
        if ($segmentIds) {
            $this->_getAdminSession()
                ->setMassactionIds($segmentIds)
                ->setViewMode($this->getRequest()->getParam('view_mode'));
        }

        /* @var $segment Enterprise_CustomerSegment_Model_Segment */
        $segment = Mage::getModel('Enterprise_CustomerSegment_Model_Segment');

        if ($segmentId) {
            $segment->load($segmentId);
        }
        if ($this->_getAdminSession()->getMassactionIds()) {
            $segment->setMassactionIds($this->_getAdminSession()->getMassactionIds());
            $segment->setViewMode($this->_getAdminSession()->getViewMode());
        }
        if (!$segment->getId() && !$segment->getMassactionIds()) {
            if ($outputMessage) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError(
                    __('You requested the wrong customer segment.')
                );
            }
            return false;
        }
        Mage::register('current_customer_segment', $segment);

        $websiteIds = $this->getRequest()->getParam('website_ids');
        if (!is_null($websiteIds) && empty($websiteIds)) {
            $websiteIds = null;
        } elseif (!is_null($websiteIds) && !empty($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        Mage::register('filter_website_ids', $websiteIds);

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
        $this->_title(__('Customer Segment Report'));

        $this->_initAction()
            ->_addContent(
                $this->getLayout()->createBlock('Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment')
            )
            ->renderlayout();
    }

    /**
     * Detail Action of customer segment
     *
     */
    public function detailAction()
    {
        $this->_title(__('Customer Segment Report'));

        if ($this->_initSegment()) {

            // Add help Notice to Combined Report
            if ($this->_getAdminSession()->getMassactionIds()) {
                $collection = Mage::getResourceModel('Enterprise_CustomerSegment_Model_Resource_Segment_Collection')
                    ->addFieldToFilter(
                        'segment_id',
                        array('in' => $this->_getAdminSession()->getMassactionIds())
                    );

                $segments = array();
                foreach ($collection as $item) {
                    $segments[] = $item->getName();
                }
                /* @translation __('Viewing combined "%1" report from segments: %2') */
                if ($segments) {
                    $viewModeLabel = $this->_objectManager->get('Enterprise_CustomerSegment_Helper_Data')->getViewModeLabel(
                        $this->_getAdminSession()->getViewMode()
                    );
                    Mage::getSingleton('Magento_Adminhtml_Model_Session')->addNotice(
                        __('Viewing combined "%1" report from segments: %2.', $viewModeLabel, implode(', ', $segments))
                    );
                }
            }

            $this->_title(__('Details'));

            $this->_initAction()->renderLayout();
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
                if ($segment->getApplyTo() != Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                    $segment->matchCustomers();
                }
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addSuccess(
                    __('Customer Segment data has been refreshed.')
                );
                $this->_redirect('*/*/detail', array('_current' => true));
                return;
            } catch (Magento_Core_Exception $e) {
                Mage::getSingleton('Magento_Adminhtml_Model_Session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/detail', array('_current' => true));
        return;
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
                ->createBlock('Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid')
                ->getExcelFile($fileName);
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
                ->createBlock('Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid')
                ->getCsvFile();
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
        $grid = $this->getLayout()
            ->createBlock('Enterprise_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail_Grid');
        $this->getResponse()->setBody($grid->toHtml());
    }

    /**
     * Retrieve admin session model
     *
     * @return Magento_Backend_Model_Auth_Session
     */
    protected function _getAdminSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = Mage::getModel('Magento_Backend_Model_Auth_Session');
        }
        return $this->_adminSession;
    }

    /**
     * Check the permission to run it
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return  $this->_authorization->isAllowed('Enterprise_CustomerSegment::customersegment')
                && $this->_objectManager->get('Enterprise_CustomerSegment_Helper_Data')->isEnabled();
    }
}
