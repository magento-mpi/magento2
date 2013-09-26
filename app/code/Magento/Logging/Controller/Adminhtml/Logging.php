<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log and archive grids controller
 */
class Magento_Logging_Controller_Adminhtml_Logging extends Magento_Adminhtml_Controller_Action
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * Event model factory
     *
     * @var Magento_Logging_Model_EventFactory
     */
    protected $_eventFactory;

    /**
     * Archive model factory
     *
     * @var Magento_Logging_Model_ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * Construct
     *
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Logging_Model_EventFactory $eventFactory
     * @param Magento_Logging_Model_ArchiveFactory $archiveFactory
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Logging_Model_EventFactory $eventFactory,
        Magento_Logging_Model_ArchiveFactory $archiveFactory
    ) {
        parent::__construct($context);

        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        $this->_archiveFactory = $archiveFactory;
    }

    /**
     * Log page
     */
    public function indexAction()
    {
        $this->_title(__('Report'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->renderLayout();
    }

    /**
     * Log grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * View logging details
     */
    public function detailsAction()
    {
        $eventId = $this->getRequest()->getParam('event_id');
        /** @var Magento_Logging_Model_Event $model */
        $model = $this->_eventFactory->create()
            ->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->_title(__("Log Entry #%1", $eventId));

        $this->_coreRegistry->register('current_event', $model);

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->renderLayout();
    }

    /**
     * Export log to CSV
     */
    public function exportCsvAction()
    {
        $this->loadLayout();
        $fileName = 'log.csv';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('logging.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getCsvFile($fileName));
    }

    /**
     * Export log to MSXML
     */
    public function exportXmlAction()
    {
        $this->loadLayout();
        $fileName = 'log.xml';
        /** @var Magento_Backend_Block_Widget_Grid_ExportInterface $exportBlock */
        $exportBlock = $this->getLayout()->getChildBlock('logging.grid', 'grid.export');
        $this->_prepareDownloadResponse($fileName, $exportBlock->getExcelFile($fileName));
    }

    /**
     * Archive page
     */
    public function archiveAction()
    {
        $this->_title(__('Admin Actions Archive'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_backups');
        $this->renderLayout();
    }

    /**
     * Archive grid ajax action
     */
    public function archiveGridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Download archive file
     */
    public function downloadAction()
    {
        $archive = $this->_archiveFactory->create()->loadByBaseName(
            $this->getRequest()->getParam('basename')
        );
        if ($archive->getFilename()) {
            $this->_prepareDownloadResponse($archive->getBaseName(), $archive->getContents(), $archive->getMimeType());
        }
    }

    /**
     * permissions checker
     */
    protected function _isAllowed()
    {
        switch ($this->getRequest()->getActionName()) {
            case 'archive':
            case 'download':
            case 'archiveGrid':
                return $this->_authorization->isAllowed('Magento_Logging::backups');
                break;
            case 'grid':
            case 'exportCsv':
            case 'exportXml':
            case 'details':
            case 'index':
                return $this->_authorization->isAllowed('Magento_Logging::magento_logging_events');
                break;
        }
    }
}
