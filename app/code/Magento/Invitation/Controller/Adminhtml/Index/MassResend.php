<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class MassResend extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Action for mass-resending invitations
     *
     * @return void
     * @throws \Magento\Framework\Model\Exception
     */
    public function execute()
    {
        try {
            $invitationsPost = $this->getRequest()->getParam('invitations', []);
            if (empty($invitationsPost) || !is_array($invitationsPost)) {
                throw new \Magento\Framework\Model\Exception(__('Please select invitations.'));
            }
            $collection = $this->_invitationFactory->create()->getCollection()->addFieldToFilter(
                'invitation_id',
                ['in' => $invitationsPost]
            )->addCanBeSentFilter();
            $found = 0;
            $sent = 0;
            $customerExists = 0;
            foreach ($collection as $invitation) {
                try {
                    $invitation->makeSureCanBeSent();
                    $found++;
                    if ($invitation->sendInvitationEmail()) {
                        $sent++;
                    }
                } catch (\Magento\Framework\Model\Exception $e) {
                    // jam all exceptions with codes
                    if (!$e->getCode()) {
                        throw $e;
                    }
                    // close irrelevant invitations
                    if ($e->getCode() === \Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS) {
                        $customerExists++;
                        $invitation->cancel();
                    }
                }
            }
            if (!$found) {
                $this->messageManager->addError(__('No invitations have been resent.'));
            }
            if ($sent) {
                $this->messageManager->addSuccess(__('We sent %1 of %2 invitations.', $sent, $found));
            }
            $failed = $found - $sent;
            if ($failed) {
                $this->messageManager->addError(__('Something went wrong sending %1 invitations.', $failed));
            }
            if ($customerExists) {
                $this->messageManager->addNotice(
                    __('We discarded %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/');
    }
}
