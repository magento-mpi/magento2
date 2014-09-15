<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Product\Compare;

class Add extends \Magento\Catalog\Controller\Product\Compare
{
    /**
     * Add item to compare list
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            $this->getResponse()->setRedirect($this->_redirect->getRefererUrl());
            return;
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product = $this->_productFactory->create();
            $product->setStoreId($this->_storeManager->getStore()->getId())->load($productId);

            if ($product->getId()) {
                $this->_catalogProductCompareList->addProduct($product);
                $productName = $this->_objectManager->get('Magento\Framework\Escaper')->escapeHtml($product->getName());
                $this->messageManager->addSuccess(__('You added product %1 to the comparison list.', $productName));
                $this->_eventManager->dispatch('catalog_product_compare_add_product', array('product' => $product));
            }

            $this->_objectManager->get('Magento\Catalog\Helper\Product\Compare')->calculate();
        }
        $this->getResponse()->setRedirect($this->_redirect->getRedirectUrl());
    }
}
