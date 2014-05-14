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
 * Gift card account block in cart
 */
class Giftcardaccount extends Form
{
    /**
     * Add gift cards button
     *
     * @var string
     */
    protected $addGiftCardButton = '.giftcard .action.add';

    /**
     * Locator for gift card account link
     *
     * @var string
     */
    protected $giftCardsSection = '.giftcard .title';

    /**
     * Fill gift card in cart
     *
     * @param string $code
     * @return void
     */
    public function addGiftCard($code)
    {
        $this->_rootElement->find($this->giftCardsSection)->click();
        $this->_fill($this->dataMapping(['code' => $code]));
        $this->_rootElement->find($this->addGiftCardButton)->click();
    }
}
