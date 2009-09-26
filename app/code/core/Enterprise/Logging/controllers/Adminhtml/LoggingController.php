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
 * @package     Enterprise_Logging
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
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
        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_logging');
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
        $eventId = $this->getRequest()->getParam('event_id');
        $model   = Mage::getModel('enterprise_logging/event')
            ->load($eventId);
        if (!$model->getId()) {
            $this->_redirect('*/*/');
            return;
        }
        Mage::register('current_event', $model);

        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Export log to CSV
     */
    public function exportCsvAction()
    {
        $this->_prepareDownloadResponse('log.csv',
            $this->getLayout()->createBlock('enterprise_logging/adminhtml_index_grid')->getCsv()
        );
    }

    /**
     * Export log to MSXML
     */
    public function exportXmlAction()
    {
        $this->_prepareDownloadResponse('log.xml',
            $this->getLayout()->createBlock('enterprise_logging/adminhtml_index_grid')->getXml()
        );
    }

    /**
     * Archive page
     */
    public function archiveAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_logging');
        $this->renderLayout();
    }

    /**
     * Archive grid ajax action
     */
    public function archiveGridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Download archive file
     */
    public function downloadAction()
    {
        $archive = Mage::getModel('enterprise_logging/archive')->loadByBaseName(
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
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/enterprise_logging/backups');
                break;
            case 'grid':
            case 'exportCsv':
            case 'exportXml':
            case 'details':
            case 'index':
                return Mage::getSingleton('admin/session')->isAllowed('admin/system/enterprise_logging/events');
                break;
        }

    }
}
