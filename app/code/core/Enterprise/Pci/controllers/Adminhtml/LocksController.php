<?php
class Enterprise_Pci_Adminhtml_LocksController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('system/acl_locks');

        $this->renderLayout();
    }

    public function gridAction()
    {

    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('system/acl/locks');
    }
}
