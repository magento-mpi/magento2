<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Delete extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Delete current gift wrapping
     * This action can be performed on 'Edit Gift Wrapping' page
     *
     * @return void
     */
    public function execute()
    {
        $wrapping = $this->_objectManager->create('Magento\GiftWrapping\Model\Wrapping');
        $wrapping->load($this->getRequest()->getParam('id', false));
        if ($wrapping->getId()) {
            try {
                $wrapping->delete();
                $this->messageManager->addSuccess(__('You deleted the gift wrapping.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['_current' => true]);
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
