<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order;

use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Mtf\Block\Block;

/**
 * Class Create
 * Adminhtml GiftMessage order create block.
 *
 */
class Create extends Block
{
    /**
     * Sales order create items block.
     *
     * @var string
     */
    protected $itemsBlock = '#order-items';

    /**
     * Fill order items gift messages.
     *
     * @param array $products
     * @param GiftMessage $giftMessage
     */
    public function fillGiftMessageForItems(array $products, GiftMessage $giftMessage)
    {
        /** @var \Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Items $items */
        $items = $this->blockFactory->create(
            'Magento\GiftMessage\Test\Block\Adminhtml\Order\Create\Items',
            ['element' => $this->_rootElement->find($this->itemsBlock)]
        );
        foreach ($products as $product) {
            $items->getItemProduct($product)->fillGiftMessageForm($giftMessage);
        }
    }
}
