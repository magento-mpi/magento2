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
class Mage_Adminhtml_SystemController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('system');
        $this->_addBreadcrumb(__('System'), __('System'));
        $this->renderLayout();
    }

    public function setStoreAction()
    {
        $storeId = (int) $this->getRequest()->getParam('store');
        $referer = $this->getRequest()->getServer('HTTP_REFERER', Mage::getUrl('*'));

        if ($storeId) {
            Mage::getSingleton('adminhtml/session')->setStoreId($storeId);
        }
        $this->getResponse()->setRedirect($referer);
    }
}