<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Service\V1\Data;

/**
 * Gift Message data object
 *
 * @codeCoverageIgnore
 */
class Message extends \Magento\Framework\Service\Data\AbstractSimpleObject
{
    const GIFT_MESSAGE_ID = 'gift_message_id';

    const SENDER = 'sender';

    const RECIPIENT = 'recipient';

    const MESSAGE = 'message';

    const CUSTOMER_ID = 'customer_id';

    /**
     * Get gift message id
     *
     * @return int|null
     */
    public function getGiftMessageId()
    {
        return $this->_get(self::GIFT_MESSAGE_ID);
    }

    /**
     * Get customer id
     *
     * @return int|null
     */
    public function getCustomerId()
    {
        return $this->_get(self::CUSTOMER_ID);
    }

    /**
     * Sender name
     *
     * @return string
     */
    public function getSender()
    {
        return $this->_get(self::SENDER);
    }

    /**
     * Recipient name
     *
     * @return string
     */
    public function getRecipient()
    {
        return $this->_get(self::RECIPIENT);
    }

    /**
     * Message text
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }
}
