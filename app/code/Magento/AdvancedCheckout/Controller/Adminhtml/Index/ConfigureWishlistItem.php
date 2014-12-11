<?php
/**
 *
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\AdvancedCheckout\Controller\Adminhtml\Index;

use Magento\Framework\Model\Exception;

class ConfigureWishlistItem extends ConfigureOrderedItem
{
    /**
     * Create item
     *
     * @param string $itemId
     * @return \Magento\Wishlist\Model\Item
     * @throws \Magento\Framework\Model\Exception
     */
    protected function createItem($itemId)
    {
        if (!$itemId) {
            throw new Exception(__('The wish list item id is not received.'));
        }

        $item = $this->_objectManager->create(
            'Magento\Wishlist\Model\Item'
        )->loadWithOptions(
            $itemId,
            'info_buyRequest'
        );
        if (!$item->getId()) {
            throw new Exception(__('The wish list item is not loaded.'));
        }
        return $item;
    }
}
