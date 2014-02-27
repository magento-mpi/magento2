<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer Segment reports controller
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Controller\Adminhtml\Report\Customer;

class Customersegment
    extends \Magento\Backend\App\Action
{
    /**
     * Admin session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\CustomerSegment\Model\Resource\Segment\CollectionFactory $collectionFactory,
        \Magento\Registry $coreRegistry,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        $this->_collectionFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * Init layout and adding breadcrumbs
     *
     * @return \Magento\CustomerSegment\Controller\Adminhtml\Report\Customer\Customersegment
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_CustomerSegment::report_customers_segment')
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
     * @return \Magento\CustomerSegment\Model\Segment|false
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

        /* @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = $this->_objectManager->create('Magento\CustomerSegment\Model\Segment');
        if ($segmentId) {
            $segment->load($segmentId);
        }
        if ($this->_getAdminSession()->getMassactionIds()) {
            $segment->setMassactionIds($this->_getAdminSession()->getMassactionIds());
            $segment->setViewMode($this->_getAdminSession()->getViewMode());
        }
        if (!$segment->getId() && !$segment->getMassactionIds()) {
            if ($outputMessage) {
                $this->messageManager->addError(__('You requested the wrong customer segment.'));
            }
            return false;
        }
        $this->_coreRegistry->register('current_customer_segment', $segment);

        $websiteIds = $this->getRequest()->getParam('website_ids');
        if (!is_null($websiteIds) && empty($websiteIds)) {
            $websiteIds = null;
        } elseif (!is_null($websiteIds) && !empty($websiteIds)) {
            $websiteIds = explode(',', $websiteIds);
        }
        $this->_coreRegistry->register('filter_website_ids', $websiteIds);

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
        $this->_title->add(__('Customer Segment Report'));

        $this->_initAction();
        $this->_view->renderLayout();
    }

    /**
     * Detail Action of customer segment
     *
     */
    public function detailAction()
    {
        $this->_title->add(__('Customer Segment Report'));

        if ($this->_initSegment()) {
            // Add help Notice to Combined Report
            if ($this->_getAdminSession()->getMassactionIds()) {
                $collection = $this->_collectionFactory->create()
                    ->addFieldToFilter(
                        'segment_id',
                        array(
                            'in' => $this->_getAdminSession()->getMassactionIds(),
                        )
                    );

                $segments = array();
                foreach ($collection as $item) {
                    $segments[] = $item->getName();
                }
                /* @translation __('Viewing combined "%1" report from segments: %2') */
                if ($segments) {
                    $viewModeLabel = $this->_objectManager->get('Magento\CustomerSegment\Helper\Data')
                        ->getViewModeLabel($this->_getAdminSession()->getViewMode());
                    $this->messageManager->addNotice(
                        __('Viewing combined "%1" report from segments: %2.', $viewModeLabel, implode(', ', $segments))
                    );
                }
            }

            $this->_title->add(__('Details'));

            $this->_initAction();
            $this->_view->renderLayout();
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
                if ($segment->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                    $segment->matchCustomers();
                }
                $this->messageManager->addSuccess(__('Customer Segment data has been refreshed.'));
                $this->_redirect('*/*/detail', array('_current' => true));
                return;
            } catch (\Magento\Core\Exception $e) {
                $this->messageManager->addError($e->getMessage());
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
            $this->_view->loadLayout();
            $content = $this->_view->getLayout()
                ->getChildBlock('report.customersegment.detail.grid', 'grid.export');
            return $this->_fileFactory->create(
                $fileName,
                $content->getExcelFile($fileName),
                \Magento\App\Filesystem::VAR_DIR
            );
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
            $this->_view->loadLayout();
            $fileName = 'customersegment_customers.csv';
            $content = $this->_view->getLayout()
                ->getChildBlock('report.customersegment.detail.grid', 'grid.export');
            return $this->_fileFactory->create(
                $fileName,
                $content->getCsvFile($fileName),
                \Magento\App\Filesystem::VAR_DIR
            );
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
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Retrieve admin session model
     *
     * @return \Magento\Backend\Model\Auth\Session
     */
    protected function _getAdminSession()
    {
        if (is_null($this->_adminSession)) {
            $this->_adminSession = $this->_objectManager->create('Magento\Backend\Model\Auth\Session');
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
        return  $this->_authorization->isAllowed('Magento_CustomerSegment::customersegment')
                && $this->_objectManager->get('Magento\CustomerSegment\Helper\Data')->isEnabled();
    }
}
