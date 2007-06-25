<?php
/**
 * System admin controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_SystemController extends Mage_Core_Controller_Front_Action 
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->getLayout()->getBlock('menu')->setActive('system');
        $this->getLayout()->getBlock('breadcrumbs')
            ->addLink(__('system'), __('system title'));
        $this->renderLayout();
    }
    
    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $referer = $this->getRequest()->getServer('HTTP_REFERER', Mage::getUrl('adminhtml'));
        
        if ($storeId) {
            Mage::getSingleton('adminhtml/session')->setStoreId($storeId);
        }
        $this->getResponse()->setRedirect($referer);
    }
}
