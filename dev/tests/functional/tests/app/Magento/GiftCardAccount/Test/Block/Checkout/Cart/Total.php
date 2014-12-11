<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return $this->escapeCurrency($this->_rootElement->find($this->giftCardDiscount)->getText());
    }

    /**
     * Escape currency in price
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
