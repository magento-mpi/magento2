<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class RemoveAllFailed extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Remove all failed items from storage
     *
     * @return void
     */
    public function execute()
    {
        $this->_getFailedItemsCart()->removeAllAffectedItems();
        $this->messageManager->addSuccess(__('You removed the items.'));
        $this->_redirect('checkout/cart');
    }
}
