<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backup admin controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_System_BackupController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
    	if($this->getRequest()->getParam('ajax')) {
    		$this->_forward('grid');
    		return;
    	}

        $this->loadLayout();
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('System'), Mage::helper('adminhtml')->__('System'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Tools'), Mage::helper('adminhtml')->__('Tools'));
        $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Backups'), Mage::helper('adminhtml')->__('Backup'));

        $this->_addContent($this->getLayout()->createBlock('adminhtml/backup', 'backup'));

        $this->renderLayout();
    }

    /**
     * Backup list action
     */
    public function gridAction()
    {
        $this->getResponse()->setBody($this->getLayout()->createBlock('adminhtml/backup_grid')->toHtml());
    }

    /**
     * Create backup action
     */
    public function createAction()
    {
        $backup = Mage::getModel('backup/backup')
                ->setTime(time())
                ->setType('db')
                ->setPath(Mage::getBaseDir("var") . DS . "backups");

        try {
    	    $dbDump = Mage::getModel('backup/db')->renderSql();
    	    $backup->setFile($dbDump);
            $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Backup successfully created'));
        }
        catch (Exception  $e) {
        	 // Nothing
        }
        $this->_redirect('*/*');
    }

    /**
     * Download backup action
     */
    public function downloadAction()
    {
        $backup = Mage::getModel('backup/backup')
                ->setTime((int)$this->getRequest()->getParam('time'))
                ->setType($this->getRequest()->getParam('type'))
                ->setPath(Mage::getBaseDir("var") . DS . "backups");

        if (!$backup->exists()) {
            $this->_redirect('*/*');
        }

        if ($this->getRequest()->getParam('file') == 'gz') {
            $fileName = 'backup-' . date('YmdHis', $backup->getTime()) . '.sql.gz';
            $fileContent = gzencode($backup->getFile(),7);
        } else {
            $fileName = 'backup-' . date('YmdHis', $backup->getTime()) . '.sql';
            $fileContent = $backup->getFile();
        }
        
        $this->_prepareDownloadResponse($fileName, $fileContent);
    }

    /**
     * Delete backup action
     */
    public function deleteAction()
    {
        try {
	    	$backup = Mage::getModel('backup/backup')
	                ->setTime((int)$this->getRequest()->getParam('time'))
	                ->setType($this->getRequest()->getParam('type'))
	                ->setPath(Mage::getBaseDir("var") . DS . "backups")
	                ->deleteFile();

	        $this->_getSession()->addSuccess(Mage::helper('adminhtml')->__('Backup record was deleted'));
        }
        catch (Exception $e) {
        		// Nothing
        }

        $this->_redirect('*/*/');

    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('system/tools/backup');
    }

    /**
     * Retrive adminhtml session model
     *
     * @return Mage_Adminhtml_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('adminhtml/session');
    }

}
