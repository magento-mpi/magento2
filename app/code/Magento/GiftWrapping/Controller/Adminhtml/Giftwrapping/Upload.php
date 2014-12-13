<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Upload extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Upload temporary gift wrapping image
     *
     * @return void
     */
    public function execute()
    {
        $wrappingRawData = $this->_prepareGiftWrappingRawData($this->getRequest()->getPost('wrapping'));
        if ($wrappingRawData) {
            try {
                $model = $this->_initModel();
                $model->addData($wrappingRawData);
                try {
                    $model->attachUploadedImage('image_name', true);
                } catch (\Exception $e) {
                    throw new \Magento\Framework\Model\Exception(__('You have not updated the image.'));
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_getSession()->setFormData($wrappingRawData);
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save the gift wrapping."));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }

        if (isset($model) && $model->getId()) {
            $this->_forward('edit');
        } else {
            $this->_forward('new');
        }
    }
}
