<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class AdvancedAdd extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Add to cart products, which SKU specified in request
     *
     * @return void
     */
    public function execute()
    {
        // check empty data
        /** @var $helper \Magento\AdvancedCheckout\Helper\Data */
        $helper = $this->_objectManager->get('Magento\AdvancedCheckout\Helper\Data');
        $items = $this->getRequest()->getParam('items');
        foreach ($items as $k => $item) {
            if (empty($item['sku'])) {
                unset($items[$k]);
            }
        }
        if (empty($items) && !$helper->isSkuFileUploaded($this->getRequest())) {
            $this->messageManager->addError($helper->getSkuEmptyDataMessageText());
            $this->_redirect('checkout/cart');
            return;
        }

        try {
            // perform data
            $cart = $this->_getFailedItemsCart()->prepareAddProductsBySku($items)->saveAffectedProducts();

            $this->messageManager->addMessages($cart->getMessages());

            if ($cart->hasErrorMessage()) {
                throw new \Magento\Framework\Model\Exception($cart->getErrorMessage());
            }
        } catch (\Magento\Framework\Model\Exception $e) {
            $this->messageManager->addException($e, $e->getMessage());
        }

        $this->_redirect('checkout/cart');
    }
}
