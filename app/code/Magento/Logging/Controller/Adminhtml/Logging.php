<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
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
        $model   = \Mage::getModel('Magento\Logging\Model\Event')
            ->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        $this->_title(__("Log Entry #%1", $eventId));

        \Mage::register('current_event', $model);

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
        $archive = \Mage::getModel('Magento\Logging\Model\Archive')->loadByBaseName(
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
