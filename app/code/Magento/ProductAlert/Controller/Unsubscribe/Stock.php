<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ProductAlert\Controller\Unsubscribe;

class Stock extends \Magento\ProductAlert\Controller\Unsubscribe
{
    /**
     * @return void
     */
    public function execute()
    {
        $productId = (int)$this->getRequest()->getParam('product');

        if (!$productId) {
            $this->_redirect('');
            return;
        }

        /* @var $product \Magento\Catalog\Model\Product */
        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $this->messageManager->addError(__('The product was not found.'));
            $this->_redirect('customer/account/');
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
            )->loadByParam();
            if ($model->getId()) {
                $model->delete();
            }
            $this->messageManager->addSuccess(__('You will no longer receive stock alerts for this product.'));
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Unable to update the alert subscription.'));
        }
        $this->getResponse()->setRedirect($product->getProductUrl());
    }
}
