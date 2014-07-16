<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class Delete extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Delete Rate and Data
     *
     * @return bool
     */
    public function execute()
    {
        if ($rateId = $this->getRequest()->getParam('rate')) {
            $rateModel = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate')->load($rateId);
            if ($rateModel->getId()) {
                try {
                    $rateModel->delete();

                    $this->messageManager->addSuccess(__('The tax rate has been deleted.'));
                    $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                    return true;
                } catch (\Magento\Framework\Model\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addError(__('Something went wrong deleting this rate.'));
                }
                if ($referer = $this->getRequest()->getServer('HTTP_REFERER')) {
                    $this->getResponse()->setRedirect($referer);
                } else {
                    $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                }
            } else {
                $this->messageManager->addError(
                    __('Something went wrong deleting this rate because of an incorrect rate ID.')
                );
                $this->getResponse()->setRedirect($this->getUrl('tax/*/'));
            }
        }
    }
}
