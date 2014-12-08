<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\AbstractAction;

class SaveInvitation extends \Magento\Invitation\Controller\Adminhtml\Index
{
    /**
     * Edit invitation's information
     *
     * @return AbstractAction|void
     */
    public function execute()
    {
        try {
            $invitation = $this->_initInvitation();

            if ($this->getRequest()->isPost()) {
                $email = $this->getRequest()->getParam('email');

                $invitation->setMessage($this->getRequest()->getParam('message'))->setEmail($email);

                $result = $invitation->validate();
                //checking if there was validation
                if (is_array($result) && !empty($result)) {
                    foreach ($result as $message) {
                        $this->messageManager->addError($message);
                    }
                    return $this->_redirect('invitations/*/view', ['_current' => true]);
                }

                //If there was no validation errors trying to save
                $invitation->save();

                $this->messageManager->addSuccess(__('The invitation has been saved.'));
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $this->_redirect('invitations/*/view', ['_current' => true]);
    }
}
