<?php
/**
 * Admin index controller
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_IndexController extends Mage_Core_Controller_Front_Action
{
    function indexAction()
    {
        $this->loadLayout('admin'); 
        $this->renderLayout();
    }
    
    function applyDbUpdatesAction()
    {
        Mage_Core_Model_Resource_Setup::applyAllUpdates();
        echo "Successfully updated.";
    }
    
    public function loginAction()
    {
        $block = Mage::getModel('core/layout')->createBlock('core/template', 'root')
                ->setTemplate('admin/login.phtml')
                ->assign('username', '');
            
        $this->getResponse()->setBody($block->toHtml());
    }
    
    public function logoutAction()
    {
        $auth = Mage::getSingleton('admin/session')->unsetAll();
        $this->getResponse()->setRedirect(Mage::getUrl('admin'));
    }
}