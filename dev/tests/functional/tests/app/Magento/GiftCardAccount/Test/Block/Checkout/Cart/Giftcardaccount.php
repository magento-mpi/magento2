<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block\Checkout\Cart;

use Mtf\Block\Form;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount as GiftCardAccountFixture;

/**
 * Class Giftcardaccount
 *
 * @package Magento\GiftCardAccount\Test\Block\Checkout\Cart
 */
class Giftcardaccount extends Form
{
    /**
     * Add gift cards button
     *
     * @var string
     */
    protected $addGiftCard = '.giftcard .action.add';

    /**
     * @var string $giftCardsSection
     */
    protected $giftCardsSection = '.giftcard .title';

    /**
     * @var string $giftCardsStatusPrice
     */
    protected $giftCardsStatusPrice = '.giftcard #giftcard-balance-lookup .price';

    /**
     * @param GiftCardAccountFixture $fixture
     * @return string
     */
    public function fillGiftCardInCart(GiftCardAccountFixture $fixture)
    {
        $this->_rootElement->find($this->giftCardsSection)->click();
        $this->fill($fixture);
        $this->_rootElement->find($this->addGiftCard)->click();
    }
}
