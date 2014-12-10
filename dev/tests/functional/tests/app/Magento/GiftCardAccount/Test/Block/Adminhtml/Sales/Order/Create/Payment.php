<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\GiftCardAccount\Test\Block\Adminhtml\Sales\Order\Create;

use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Gift card account block in order new page.
 */
class Payment extends Form
{
    /**
     * Selector for "Add Gift Card" button.
     *
     * @var string
     */
    protected $addGiftCardButton = "//button[preceding::input[contains(@id, 'giftcardaccount')]]";

    /**
     * Click "Add Gift Card" button.
     *
     * @return void
     */
    protected function clickAddGiftCard()
    {
        $this->_rootElement->find($this->addGiftCardButton, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Apply gift card account on order new page.
     *
     * @param GiftCardAccount $giftCard
     * @return void
     */
    public function applyGiftCardAccount(GiftCardAccount $giftCard)
    {
        $this->fill($giftCard);
        $this->clickAddGiftCard();
    }
}
