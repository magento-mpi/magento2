<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

class AddToCart extends \Magento\AdvancedCheckout\Controller\Adminhtml\Index
{
    /**
     * Add products to quote, ajax
     * Currently not used, as all requests now go through loadBlock action
     *
     * @return void
     */
    public function execute()
    {
        try {
            $this->_isModificationAllowed();
            $this->_initData();
            if ($this->_redirectFlag) {
                return;
            }

            $cart = $this->getCartModel();
            $customer = $this->_registry->registry('checkout_current_customer');
            $store = $this->_registry->registry('checkout_current_store');

            $source = $this->_objectManager->get(
                'Magento\Core\Helper\Data'
            )->jsonDecode(
                $this->getRequest()->getPost('source')
            );

            // Reorder products
            if (isset($source['source_ordered']) && is_array($source['source_ordered'])) {
                foreach ($source['source_ordered'] as $orderItemId => $qty) {
                    $orderItem = $this->_objectManager->create('Magento\Sales\Model\Order\Item')->load($orderItemId);
                    $cart->reorderItem($orderItem, $qty);
                }
                unset($source['source_ordered']);
            }

            // Add new products
            if (is_array($source)) {
                foreach ($source as $products) {
                    if (is_array($products)) {
                        foreach ($products as $productId => $qty) {
                            $cart->addProduct($productId, $qty);
                        }
                    }
                }
            }

            // Collect quote totals and save it
            $cart->saveQuote();

            // Remove items from wishlist
            if (isset($source['source_wishlist']) && is_array($source['source_wishlist'])) {
                $wishlist = $this->_objectManager->create(
                    'Magento\Wishlist\Model\Wishlist'
                )->loadByCustomerId(
                    $customer->getId()
                )->setStore(
                    $store
                )->setSharedStoreIds(
                    $store->getWebsite()->getStoreIds()
                );
                if ($wishlist->getId()) {
                    $quoteProductIds = array();
                    foreach ($cart->getQuote()->getAllItems() as $item) {
                        $quoteProductIds[] = $item->getProductId();
                    }
                    foreach ($source['source_wishlist'] as $productId => $qty) {
                        if (in_array($productId, $quoteProductIds)) {
                            $wishlistItem = $this->_objectManager->create(
                                'Magento\Wishlist\Model\Item'
                            )->loadByProductWishlist(
                                $wishlist->getId(),
                                $productId,
                                $wishlist->getSharedStoreIds()
                            );
                            if ($wishlistItem->getId()) {
                                $wishlistItem->delete();
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            $this->_processException($e);
        }
    }
}
