<?
/**
 * Backup admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Ivan Chepurnyi <mitch@varien.com>
 */
class Mage_Adminhtml_System_BackupController extends Mage_Adminhtml_Controller_Action 
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(__('system'), __('system title'), Mage::getUrl('adminhtml',array('controller'=>'system')));
        $this->_addBreadcrumb(__('backup'), __('backup title'));
        
        $this->_addContent($this->getLayout()->createBlock('adminhtml/backup', 'backup'));
        
        $this->renderLayout();
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
        
        $dbDump = Mage::getModel('backup/db')->renderSql();
        $backup->setFile($dbDump);
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
        
        header("Content-Disposition: attachment; filename=$fileName");
        header("Content-Type: application/octet-stream");
        header("Content-Length: ".strlen($fileContent));
        $this->getResponse()->setBody($fileContent);
    }
    
    /**
     * Delete backup action
     */
    public function deleteAction()
    {
        $backup = Mage::getModel('backup/backup')
                ->setTime((int)$this->getRequest()->getParam('time'))
                ->setType($this->getRequest()->getParam('type'))
                ->setPath(Mage::getBaseDir("var") . DS . "backups")
                ->deleteFile();
                
        $this->_redirect('*/*/');
            
    }
}