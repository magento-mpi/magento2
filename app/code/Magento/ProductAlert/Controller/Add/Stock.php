<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Controller\Add;

class Stock extends \Magento\ProductAlert\Controller\Add
{
    /**
     * @return void
     */
    public function execute()
    {
        $backUrl = $this->getRequest()->getParam(\Magento\Framework\App\Action\Action::PARAM_NAME_URL_ENCODED);
        $productId = (int)$this->getRequest()->getParam('product_id');
        if (!$backUrl || !$productId) {
            $this->_redirect('/');
            return;
        }

        if (!($product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId))) {
            /* @var $product \Magento\Catalog\Model\Product */
            $this->messageManager->addError(__('There are not enough parameters.'));
            $this->getResponse()->setRedirect($backUrl);
            return;
        }

        try {
            $model = $this->_objectManager->create(
                'Magento\ProductAlert\Model\Stock'
            )->setCustomerId(
                $this->_customerSession->getCustomerId()
            )->setProductId(
                $product->getId()
            )->setWebsiteId(
                $this->_objectManager->get('Magento\Framework\StoreManagerInterface')->getStore()->getWebsiteId()
            );
            $model->save();
            $this->messageManager->addSuccess(__('Alert subscription has been saved.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
