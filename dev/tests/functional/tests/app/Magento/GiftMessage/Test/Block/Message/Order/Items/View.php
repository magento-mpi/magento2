<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Message\Order\Items;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Gift message block for order's items on order view page
 */
class View extends Block
{
    /**
     * Gift message sender selector
     *
     * @var string
     */
    protected $giftMessageSenderSelector = ".gift.sender";

    /**
     * Gift message recipient selector
     *
     * @var string
     */
    protected $giftMessageRecipientSelector = ".gift.recipient";

    /**
     * Gift message text selector
     *
     * @var string
     */
    protected $giftMessageTextSelector = ".message.text";

    /**
     * Selector for "Gift Message" button
     *
     * @var string
     */
    protected $giftMessageButtonSelector = ".//td[contains(., '%s')]//a[contains(@id,'gift-message')]";

    /**
     * Selector for "Gift Message"
     *
     * @var string
     */
    protected $giftMessageForItemSelector = ".//tr[contains(., '%s')]/following-sibling::tr";

    /**
     * Get gift message for item
     *
     * @param string $itemName
     * @return array
     */
    public function getGiftMessage($itemName)
    {
        $message = [];
        $this->clickGiftMessageButton($itemName);
        $messageElement = $this->_rootElement->find(
            sprintf($this->giftMessageForItemSelector, $itemName),
            Locator::SELECTOR_XPATH
        );

        $message['sender'] = $messageElement->find($this->giftMessageSenderSelector)->getText();
        $message['recipient'] = $messageElement->find($this->giftMessageRecipientSelector)->getText();
        $message['message'] = $messageElement->find($this->giftMessageTextSelector)->getText();
        $message = preg_replace('@.*?:\s(.*)@', '\1', $message);

        return $message;
    }

    /**
     * Click "Gift Message" for special item
     *
     * @param string $itemName
     * @return void
     */
    protected function clickGiftMessageButton($itemName)
    {
        $this->_rootElement->find(
            sprintf($this->giftMessageButtonSelector, $itemName),
            Locator::SELECTOR_XPATH
        )->click();
    }
}
