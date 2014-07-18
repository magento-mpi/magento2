<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Controller\Adminhtml\Rate;

class Save extends \Magento\Tax\Controller\Adminhtml\Rate
{
    /**
     * Save Rate and Data
     *
     * @return bool
     */
    public function execute()
    {
        $ratePost = $this->getRequest()->getPost();
        if ($ratePost) {
            $rateId = $this->getRequest()->getParam('tax_calculation_rate_id');
            if ($rateId) {
                $rateModel = $this->_objectManager->get('Magento\Tax\Model\Calculation\Rate')->load($rateId);
                if (!$rateModel->getId()) {
                    unset($ratePost['tax_calculation_rate_id']);
                }
            }

            $rateModel = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate')->setData($ratePost);

            try {
                $rateModel->save();

                $this->messageManager->addSuccess(__('The tax rate has been saved.'));
                $this->getResponse()->setRedirect($this->getUrl("*/*/"));
                return true;
            } catch (\Magento\Framework\Model\Exception $e) {
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData($ratePost);
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }

            $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl($this->getUrl('*')));
            return;
        }
        $this->getResponse()->setRedirect($this->getUrl('tax/rate'));
    }
}
