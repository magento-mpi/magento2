<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Order;

/**
 * Items block on order's view page
 */
class Items extends \Magento\Sales\Test\Block\Order\Items
{
    /**
     * GiftCard discount price selector.
     *
     * @var string
     */
    protected $giftCardDiscount = '.discount > span.price';

    /**
     * Get gift card discount price.
     *
     * @return string
     */
    public function getGiftCardDiscount()
    {
        return $this->escapeCurrency($this->_rootElement->find($this->giftCardDiscount)->getText());
    }
}
