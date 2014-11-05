<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Sales\Order\PrintOrder;

use Magento\Sales\Test\Block\Order\PrintOrder\Totals;

/**
 * Class GiftCards
 * Gift card account total block in print order
 */
class GiftCards extends Totals
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

    /**
     * Escape currency in price.
     *
     * @param string $price
     * @return string|null
     */
    protected function escapeCurrency($price)
    {
        preg_match("/^\\D*\\s*([\\d,\\.]+)\\s*\\D*$/", $price, $matches);
        return (isset($matches[1])) ? $matches[1] : null;
    }
}
