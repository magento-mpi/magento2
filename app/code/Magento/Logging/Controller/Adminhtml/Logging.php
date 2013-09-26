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
namespace Magento\Logging\Controller\Adminhtml;

class Logging extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Event model factory
     *
     * @var \Magento\Logging\Model\EventFactory
     */
    protected $_eventFactory;

    /**
     * Archive model factory
     *
     * @var \Magento\Logging\Model\ArchiveFactory
     */
    protected $_archiveFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\Controller\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     * @param \Magento\Logging\Model\EventFactory $eventFactory
     * @param \Magento\Logging\Model\ArchiveFactory $archiveFactory
     */
    public function __construct(
        \Magento\Backend\Controller\Context $context,
        \Magento\Core\Model\Registry $coreRegistry,
        \Magento\Logging\Model\EventFactory $eventFactory,
        \Magento\Logging\Model\ArchiveFactory $archiveFactory
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
        /** @var \Magento\Logging\Model\Event $model */
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
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
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
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
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
