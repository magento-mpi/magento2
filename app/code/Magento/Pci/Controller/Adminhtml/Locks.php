<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pci
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locked administrators controller
 *
 */
namespace Magento\Pci\Controller\Adminhtml;

class Locks extends \Magento\Adminhtml\Controller\Action
{
    /**
     * Render page with grid
     *
     */
    public function indexAction()
    {
        $this->_title(__('Locked Users'));

        $this->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_acl_locks');
        $this->renderLayout();
    }

    /**
     * Render AJAX-grid only
     *
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();

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
                $affectedUsers = $this->_objectManager->get('Magento\Pci\Model\Resource\Admin\User')->unlock($userIds);
                $this->_objectManager->get('Magento\Adminhtml\Model\Session')
                        ->addSuccess(__('Unlocked %1 user(s).', $affectedUsers));
            }
        }
        catch (\Exception $e) {
            $this->_objectManager->get('Magento\Adminhtml\Model\Session')->addError($e->getMessage());
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
        return $this->_authorization->isAllowed('Magento_Pci::locks');
    }
}
