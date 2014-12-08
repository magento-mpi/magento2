<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping;

class Save extends \Magento\GiftWrapping\Controller\Adminhtml\Giftwrapping
{
    /**
     * Save gift wrapping
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

                $data = new \Magento\Framework\Object($wrappingRawData);
                if ($data->getData('image_name/delete')) {
                    $model->setImage('');
                    // Delete temporary image if exists
                    $model->unsTmpImage();
                } else {
                    try {
                        $model->attachUploadedImage('image_name');
                    } catch (\Exception $e) {
                        throw new \Magento\Framework\Model\Exception(__('You have not uploaded the image.'));
                    }
                }

                $model->save();
                $this->messageManager->addSuccess(__('You saved the gift wrapping.'));

                $redirectBack = $this->getRequest()->getParam('back', false);
                if ($redirectBack) {
                    $this->_redirect(
                        'adminhtml/*/edit',
                        ['id' => $model->getId(), 'store' => $model->getStoreId()]
                    );
                    return;
                }
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $this->_redirect('adminhtml/*/edit', ['id' => $model->getId()]);
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(__("We couldn't save the gift wrapping."));
                $this->_objectManager->get('Magento\Framework\Logger')->logException($e);
            }
        }
        $this->_redirect('adminhtml/*/');
    }
}
