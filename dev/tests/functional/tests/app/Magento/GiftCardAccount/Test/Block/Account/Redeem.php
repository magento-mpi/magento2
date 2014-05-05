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
 *
 * @package Magento\GiftCardAccount\Test\Block\Account
 */
class Redeem extends Block
{
    /**
     * @var string $giftcardCode
     */
    private $giftCardCode = "#giftcard-code";

    /** @var string $redeemGiftCard */
    private $redeemGiftCard = ".action.redeem";

    /**
     * Fill gift card redeem
     *
     * @param string $value
     */
    public function fillGiftCardRedeem($value)
    {
        $this->_rootElement->find($this->giftCardCode)->setValue($value);
        $this->_rootElement->find($this->redeemGiftCard)->click();
    }
}
