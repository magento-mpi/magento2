<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class AddFailedItems extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Add failed items to cart
     *
     * @return void
     */
    public function execute()
    {
        $failedItemsCart = $this->_getFailedItemsCart()->removeAllAffectedItems();
        $failedItems = $this->getRequest()->getParam('failed', array());
        $cartItems = $this->getRequest()->getParam('cart', array());
        $failedItemsCart->updateFailedItems($failedItems, $cartItems);
        $failedItemsCart->saveAffectedProducts();
        $this->_redirect('checkout/cart');
    }
}
