<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Checkout\Cart;

use Mtf\Block\Form;

/**
 * Class Total
 * Gift card account total block in cart
 */
class Total extends Form
{
    /**
     * GiftCard discount price
     *
     * @var string
     */
    protected $giftCardDiscount = '.discount .price';

    /**
     * Get giftCard discount price
     *
     * @return array|string
     */
    public function getGiftCardDiscount()
    {
        return $this->_rootElement->find($this->giftCardDiscount)->getText();
    }
}
