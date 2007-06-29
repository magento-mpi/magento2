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
class Mage_Adminhtml_BackupController extends Mage_Core_Controller_Front_Action 
{
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
    
    public function createAction()
    {
        $backup = Mage::getModel('backup/backup')
                ->setTime(time())
                ->setType('db')
                ->setPath(Mage::getBaseDir("var") . DS . "backups");
        
        $dbDump = Mage::getModel('backup/db')->renderSql();
        $backup->setFile($dbDump);
        
    }
    
    
}