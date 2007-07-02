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
class Mage_Adminhtml_System_BackupController extends Mage_Core_Controller_Front_Action 
{
    /**
     * Backup list action
     */
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('system');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('system'), __('system title'), Mage::getUrl('adminhtml',array('controller'=>'system')))
            ->addLink(__('backup'), __('backup title'));
        
        $this->getLayout()->getBlock('content')->append($this->getLayout()->createBlock('adminhtml/backup', 'backup'));
        
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
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/backup'));
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
            $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/backup'));
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
                
        $this->getResponse()->setRedirect(Mage::getUrl('adminhtml/backup'));
            
    }
}