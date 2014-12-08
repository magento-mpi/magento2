<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

class MassCancel extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Action for mass-cancelling invitations
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
            )->addCanBeCanceledFilter();
            $found = 0;
            $cancelled = 0;
            foreach ($collection as $invitation) {
                try {
                    $found++;
                    if ($invitation->canBeCanceled()) {
                        $invitation->cancel();
                        $cancelled++;
                    }
                } catch (\Magento\Framework\Model\Exception $e) {
                    // jam all exceptions with codes
                    if (!$e->getCode()) {
                        throw $e;
                    }
                }
            }
            if ($cancelled) {
                $this->messageManager->addSuccess(__('We discarded %1 of %2 invitations.', $cancelled, $found));
            }
            $failed = $found - $cancelled;
            if ($failed) {
                $this->messageManager->addNotice(__('We skipped %1 of the selected invitations.', $failed));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/');
    }
}
