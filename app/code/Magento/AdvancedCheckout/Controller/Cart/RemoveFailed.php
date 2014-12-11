<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Cart;

class RemoveFailed extends \Magento\AdvancedCheckout\Controller\Cart
{
    /**
     * Remove failed items from storage
     *
     * @return void
     */
    public function execute()
    {
        $removed = $this->_getFailedItemsCart()->removeAffectedItem(
            $this->_objectManager->get('Magento\Core\Helper\Url')->urlDecode($this->getRequest()->getParam('sku'))
        );

        if ($removed) {
            $this->messageManager->addSuccess(__('You removed the item.'));
        }

        $this->_redirect('checkout/cart');
    }
}
