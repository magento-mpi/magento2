<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype;

class Delete extends \Magento\CustomerCustomAttributes\Controller\Adminhtml\Customer\Formtype
{
    /**
     * Delete form type
     *
     * @return void
     */
    public function execute()
    {
        $formType = $this->_initFormType();
        if ($this->getRequest()->isPost() && $formType->getId()) {
            if ($formType->getIsSystem()) {
                $message = __('This system form type cannot be deleted.');
                $this->messageManager->addError($message);
            } else {
                try {
                    $formType->delete();
                    $message = __('The form type has been deleted.');
                    $this->messageManager->addSuccess($message);
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $message = __('Something went wrong deleting the form type.');
                    $this->messageManager->addException($e, $message);
                }
            }
        }
        $this->_redirect('adminhtml/*/index');
    }
}
