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

class Locks extends \Magento\Backend\App\Action
{
    /**
     * Render page with grid
     *
     */
    public function indexAction()
    {
        $this->_title->add(__('Locked Users'));

        $this->_view->loadLayout();
        $this->_setActiveMenu('Magento_Pci::system_acl_locks');
        $this->_view->renderLayout();
    }

    /**
     * Render AJAX-grid only
     *
     */
    public function gridAction()
    {
        $this->_view->loadLayout(false);
        $this->_view->renderLayout();

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
                $this->getMessageManager()->addSuccess(__('Unlocked %1 user(s).', $affectedUsers));
            }
        }
        catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/*/');
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
