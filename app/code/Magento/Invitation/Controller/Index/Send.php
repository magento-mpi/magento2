<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Index;

class Send extends \Magento\Invitation\Controller\Index
{
    /**
     * Send invitations from frontend
     *
     * @return void
     */
    public function execute()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            $customer = $this->_session->getCustomer();
            $message = isset($data['message']) ? $data['message'] : '';
            if (!$this->_config->isInvitationMessageAllowed()) {
                $message = '';
            }
            $invPerSend = $this->_config->getMaxInvitationsPerSend();
            $attempts = 0;
            $sent = 0;
            $customerExists = 0;
            foreach ($data['email'] as $email) {
                $attempts++;
                if (!\Zend_Validate::is($email, 'EmailAddress')) {
                    continue;
                }
                if ($attempts > $invPerSend) {
                    continue;
                }
                try {
                    $invitation = $this->invitationFactory->create()->setData(
                        array('email' => $email, 'customer' => $customer, 'message' => $message)
                    )->save();
                    if ($invitation->sendInvitationEmail()) {
                        $this->messageManager->addSuccess(__('You sent the invitation for %1.', $email));
                        $sent++;
                    } else {
                        // not \Magento\Framework\Model\Exception intentionally
                        throw new \Exception('');
                    }
                } catch (\Magento\Framework\Model\Exception $e) {
                    if (\Magento\Invitation\Model\Invitation::ERROR_CUSTOMER_EXISTS === $e->getCode()) {
                        $customerExists++;
                    } else {
                        $this->messageManager->addError($e->getMessage());
                    }
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Something went wrong sending an email to %1.', $email));
                }
            }
            if ($customerExists) {
                $this->messageManager->addNotice(
                    __('We did not send %1 invitation(s) addressed to current customers.', $customerExists)
                );
            }
            $this->_redirect('*/*/');
            return;
        }

        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->loadLayoutUpdates();
        $this->pageConfig->setTitle(__('Send Invitations'));
        $this->_view->renderLayout();
    }
}
