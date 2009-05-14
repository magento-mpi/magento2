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
 * @category   Enterprise
 * @package    Enterprise_Logging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Logging_Adminhtml_EventsController extends Mage_Adminhtml_Controller_Action 
{
    
    /**
     * Index action, page with grid
     */

    public function indexAction() 
    {
        $this->loadLayout()
          ->_addBreadcrumb(Mage::helper('enterprise_logging')->__('View admin logs list'), Mage::helper('enterprise_logging')->__('View admin logs list'))
          ->_addBreadcrumb('one', 'one');

        $this->_setActiveMenu('system/enterprise_logging');
        $this->renderLayout();
    }


    /**
     * Files grid
     */
    public function logAction() 
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/enterprise_logging');
        $this->renderLayout();
    }

    /**
     * Download action
     */
    public function downloadAction()
    {
        $name = $this->getRequest()->getParam('name');
        $chunks = explode(DS, $name);
        if (count($chunks) != 3 || !preg_match("%^\d{4}$%", $chunks[0]) || !preg_match("%^\d{2}$%", $chunks[1]) || 
            !preg_match("%^[a-zA-Z0-9\-\_]{0,20}\.csv$%", $chunks[2])) {
            exit("WRONG FILE NAME !");
        }

        $log = Mage::getModel('enterprise_logging/logs')
          ->setFileName($name);


        /* @var $backup Mage_Backup_Model_Backup */

        if (!$log->exists()) {
            $this->_redirect('*/*');
        }
        $fileName = $chunks[2];
        $this->_prepareDownloadResponse($fileName, null, 'application/octet-stream', $log->getSize());
        $this->getResponse()->sendHeaders();
        $log->output();
        exit();
    }

    /**
     * Flies grid handler
     */
    public function loggridAction() 
    {
        try {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_logging/events_archive_grid')->toHtml()
        );
        } catch(Exception $e) {
            echo $e;
        }
    }

    /**
     * grid
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('enterprise_logging/events_grid')->toHtml()
        );
    }

    /**
     * Export customer grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName   = 'events.csv';
        $content    = $this->getLayout()->createBlock('enterprise_logging/events_grid')
            ->getCsv();
        $this->_prepareDownloadResponse($fileName, $content);
    }


    /**
     * Export customer grid to XML format
     */
    public function exportXmlAction()
    {
        $fileName   = 'events.xml';
        $content    = $this->getLayout()->createBlock('enterprise_logging/events_grid')
            ->getXml();
        $this->_prepareDownloadResponse($fileName, $content);
    }


    /**
     * permissions checker
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/logging');
    }
}
