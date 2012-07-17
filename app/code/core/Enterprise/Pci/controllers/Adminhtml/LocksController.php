<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locked administrators controller
 *
 */
class Enterprise_Pci_Adminhtml_LocksController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Render page with grid
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Permissions'))
             ->_title($this->__('Locked Users'));

        $this->loadLayout();
        $this->_setActiveMenu('Enterprise_Pci::system_acl_locks');
        $this->renderLayout();
    }

    /**
     * Render AJAX-grid only
     *
     */
    public function gridAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('Enterprise_Pci_Block_Adminhtml_Locks_Grid')->toHtml()
        );
    }

    /**
     * Unlock specified users
     */
    public function massUnlockAction()
    {
        try {
            // unlock users
            $userIds = $this->getRequest()->getPost('unlock');
            if ($userIds && is_array($userIds)) {
                $affectedUsers = Mage::getResourceSingleton('Enterprise_Pci_Model_Resource_Admin_User')
                    ->unlock($userIds);
                Mage::getSingleton('Mage_Adminhtml_Model_Session')
                        ->addSuccess($this->__('Unlocked %d user(s).', $affectedUsers));
            }
        }
        catch (Exception $e) {
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addError($e->getMessage());
        }
        $this->_redirect('*/*/');
    }

    /**
     * Check whether access is allowed for current admin session
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->isAllowed('system/acl/locks');
    }
}
