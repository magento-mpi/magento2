<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pci\Controller\Adminhtml\Locks;

class MassUnlock extends \Magento\Pci\Controller\Adminhtml\Locks
{
    /**
     * Unlock specified users
     *
     * @return void
     */
    public function execute()
    {
        try {
            // unlock users
            $userIds = $this->getRequest()->getPost('unlock');
            if ($userIds && is_array($userIds)) {
                $affectedUsers = $this->_objectManager->get('Magento\Pci\Model\Resource\Admin\User')->unlock($userIds);
                $this->getMessageManager()->addSuccess(__('Unlocked %1 user(s).', $affectedUsers));
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('adminhtml/*/');
    }
}
