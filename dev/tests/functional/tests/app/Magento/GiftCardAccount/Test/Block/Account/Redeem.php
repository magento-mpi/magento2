<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Account;

use Mtf\Block\Block;

/**
 * Class Redeem
 * Redeem block on customer account page
 */
class Redeem extends Block
{
    /**
     * Gift card code input field
     *
     * @var string
     */
    protected $giftCardCode = '[name="giftcard_code"]';

    /**
     * Redeem button
     *
     * @var string
     */
    protected $redeemGiftCard = ".action.redeem";

    /**
     * Fill gift card redeem
     *
     * @param string $value
     * @return void
     */
    public function redeemGiftCard($value)
    {
        $this->_rootElement->find($this->giftCardCode)->setValue($value);
        $this->_rootElement->find($this->redeemGiftCard)->click();
    }
}
