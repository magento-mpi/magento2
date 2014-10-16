<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Message\Order;

/**
 * Class View
 * Gift message block for order on order view page
 */
class View extends \Magento\Sales\Test\Block\Order\View
{
    /**
     * Gift message sender selector
     *
     * @var string
     */
    protected $giftMessageSenderSelector = ".gift-sender";

    /**
     * Gift message recipient selector
     *
     * @var string
     */
    protected $giftMessageRecipientSelector = ".gift-recipient";

    /**
     * Gift message text selector
     *
     * @var string
     */
    protected $giftMessageTextSelector = ".gift-message-text";

    /**
     * Get gift message for order
     *
     * @return array
     */
    public function getGiftMessage()
    {
        $message = [];

        $message['sender'] = $this->_rootElement->find($this->giftMessageSenderSelector)->getText();
        $message['recipient'] = $this->_rootElement->find($this->giftMessageRecipientSelector)->getText();
        $message['message'] = $this->_rootElement->find($this->giftMessageTextSelector)->getText();
        $message = preg_replace('@.*?:\s(.*)@', '\1', $message);

        return $message;
    }
}
