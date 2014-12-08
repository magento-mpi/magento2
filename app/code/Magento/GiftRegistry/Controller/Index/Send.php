<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftRegistry\Controller\Index;

use Magento\Framework\Model\Exception;

class Send extends \Magento\GiftRegistry\Controller\Index
{
    /**
     * Share selected gift registry entity
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->_redirect('*/*/share', ['_current' => true]);
            return;
        }

        try {
            /** @var $entity \Magento\GiftRegistry\Model\Entity */
            $entity = $this->_initEntity()->addData($this->getRequest()->getPost());

            $result = $entity->sendShareRegistryEmails();

            if ($result->getIsSuccess()) {
                $this->messageManager->addSuccess($result->getSuccessMessage());
            } else {
                $this->messageManager->addError($result->getErrorMessage());
                $this->_getSession()->setSharingForm($this->getRequest()->getPost());
                $this->_redirect('*/*/share', ['_current' => true]);
                return;
            }
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $message = __('Something went wrong while sending email(s).');
            $this->messageManager->addException($e, $message);
        }
        $this->_redirect('*/*/');
    }
}
