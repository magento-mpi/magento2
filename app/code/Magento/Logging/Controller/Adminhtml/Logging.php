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

use \Magento\App\ResponseInterface;
use Magento\Backend\App\Action;

class Logging extends \Magento\Backend\App\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
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
     * @var \Magento\App\Response\Http\FileFactory
     */
    protected $_fileFactory;

    /**
     * Construct
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Logging\Model\EventFactory $eventFactory
     * @param \Magento\Logging\Model\ArchiveFactory $archiveFactory
     * @param \Magento\App\Response\Http\FileFactory $fileFactory
     */
    public function __construct(
        Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Logging\Model\EventFactory $eventFactory,
        \Magento\Logging\Model\ArchiveFactory $archiveFactory,
        \Magento\App\Response\Http\FileFactory $fileFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
        $this->_eventFactory = $eventFactory;
        $this->_archiveFactory = $archiveFactory;
        $this->_fileFactory = $fileFactory;
    }

    /**
     * Log page
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_title->add(__('Report'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->_view->renderLayout();
    }

    /**
     * Log grid ajax action
     *
     * @return void
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * View logging details
     *
     * @return void
     */
    public function detailsAction()
    {
        $eventId = $this->getRequest()->getParam('event_id');
        /** @var \Magento\Logging\Model\Event $model */
        $model = $this->_eventFactory->create()
            ->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('adminhtml/*/');
            return;
        }
        $this->_title->add(__("Log Entry #%1", $eventId));

        $this->_coreRegistry->register('current_event', $model);

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_events');
        $this->_view->renderLayout();
    }

    /**
     * Export log to CSV
     *
     * @return ResponseInterface
     */
    public function exportCsvAction()
    {
        $this->_view->loadLayout();
        $fileName = 'log.csv';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('logging.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getCsvFile($fileName),
            \Magento\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Export log to MSXML
     *
     * @return ResponseInterface
     */
    public function exportXmlAction()
    {
        $this->_view->loadLayout();
        $fileName = 'log.xml';
        /** @var \Magento\Backend\Block\Widget\Grid\ExportInterface $exportBlock */
        $exportBlock = $this->_view->getLayout()->getChildBlock('logging.grid', 'grid.export');
        return $this->_fileFactory->create(
            $fileName,
            $exportBlock->getExcelFile($fileName),
            \Magento\App\Filesystem::VAR_DIR
        );
    }

    /**
     * Archive page
     *
     * @return void
     */
    public function archiveAction()
    {
        $this->_title->add(__('Admin Actions Archive'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Logging::system_magento_logging_backups');
        $this->_view->renderLayout();
    }

    /**
     * Archive grid ajax action
     *
     * @return void
     */
    public function archiveGridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();
    }

    /**
     * Download archive file
     *
     * @return ResponseInterface
     */
    public function downloadAction()
    {
        $archive = $this->_archiveFactory->create()->loadByBaseName(
            $this->getRequest()->getParam('basename')
        );
        if ($archive->getFilename()) {
            return $this->_fileFactory->create(
                $archive->getBaseName(),
                $archive->getContents(),
                \Magento\App\Filesystem::VAR_DIR,
                $archive->getMimeType()
            );
        }
    }

    /**
     * permissions checker
     *
     * @return bool
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
