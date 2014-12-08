<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class Save extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Create & send new invitations
     *
     * @return void
     */
    public function execute()
    {
        try {
            // parse POST data
            if (!$this->getRequest()->isPost()) {
                $this->_redirect('invitations/*/');
                return;
            }
            $this->_getSession()->setInvitationFormData($this->getRequest()->getPost());
            $emails = preg_split('/\s+/s', $this->getRequest()->getParam('email'));
            foreach ($emails as $key => $email) {
                $email = trim($email);
                if (empty($email)) {
                    unset($emails[$key]);
                } else {
                    $emails[$key] = $email;
                }
            }
            if (empty($emails)) {
                throw new \Magento\Framework\Model\Exception(__('Please specify at least one email address.'));
            }
            if ($this->_storeManager->hasSingleStore()) {
                $storeId = $this->_storeManager->getStore(true)->getId();
            } else {
                $storeId = $this->getRequest()->getParam('store_id');
            }

            // try to send invitation(s)
            $sentCount = 0;
            $failedCount = 0;
            $customerExistsCount = 0;
            foreach ($emails as $key => $email) {
                try {
                    /** @var \Magento\Invitation\Model\Invitation $invitation */
                    $invitation = $this->_invitationFactory->create()->setData(
                        [
                            'email' => $email,
                            'store_id' => $storeId,
                            'message' => $this->getRequest()->getParam('message'),
                            'group_id' => $this->getRequest()->getParam('group_id'),
                        ]
                    )->save();
                    if ($invitation->sendInvitationEmail()) {
                        $sentCount++;
                    } else {
                        $failedCount++;
                    }
                } catch (\Magento\Framework\Model\Exception $e) {
                    if ($e->getCode()) {
                        $failedCount++;
                        if ($e->getCode() == \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS) {
                            $customerExistsCount++;
                        }
                    } else {
                        throw $e;
                    }
                }
            }
            if ($sentCount) {
                $this->messageManager->addSuccess(__('We sent %1 invitation(s).', $sentCount));
            }
            if ($failedCount) {
                $this->messageManager->addError(
                    __('Something went wrong sending %1 of %2 invitations.', $failedCount, count($emails))
                );
            }
            if ($customerExistsCount) {
                $this->messageManager->addNotice(
                    __(
                        '%1 invitation(s) were not sent, because customer accounts already exist for specified email addresses.',
                        $customerExistsCount
                    )
                );
            }
            $this->_getSession()->unsInvitationFormData();
            $this->_redirect('invitations/*/');
            return;
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/new');
    }
}
