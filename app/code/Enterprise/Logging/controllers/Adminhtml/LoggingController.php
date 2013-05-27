<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Log and archive grids controller
 */
class Enterprise_Logging_Adminhtml_LoggingController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Log page
     */
    public function indexAction()
    {
        $this->_title($this->__('Report'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Logging::system_enterprise_logging_events');
        $this->renderLayout();
    }

    /**
     * Log grid ajax action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * View logging details
     */
    public function detailsAction()
    {
        $this->_title($this->__('View Entry'));

        $eventId = $this->getRequest()->getParam('event_id');
        $model   = Mage::getModel('Enterprise_Logging_Model_Event')
            ->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        Mage::register('current_event', $model);

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Logging::system_enterprise_logging_events');
        $this->renderLayout();
    }

    /**
     * Export log to CSV
     */
    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('log.csv',
            $this->getLayout()->createBlock('Enterprise_Logging_Block_Adminhtml_Index_Grid')->getCsvFile()
        );
    }

    /**
     * Export log to MSXML
     */
    public function exportXmlAction()
    {
        $this->_prepareDownloadResponse('log.xml',
            $this->getLayout()->createBlock('Enterprise_Logging_Block_Adminhtml_Index_Grid')->getExcelFile()
        );
    }

    /**
     * Archive page
     */
    public function archiveAction()
    {
        $this->_title($this->__('Archive'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Logging::system_enterprise_logging_backups');
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
        $archive = Mage::getModel('Enterprise_Logging_Model_Archive')->loadByBaseName(
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
                return $this->_authorization->isAllowed('Enterprise_Logging::backups');
                break;
            case 'grid':
            case 'exportCsv':
            case 'exportXml':
            case 'details':
            case 'index':
                return $this->_authorization->isAllowed('Enterprise_Logging::enterprise_logging_events');
                break;
        }

    }
}
