<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftMessage\Service\V1\Data;

/**
 * Gift message data object builder
 *
 * @codeCoverageIgnore
 */
class MessageBuilder extends \Magento\Framework\Service\Data\AbstractObjectBuilder
{
    /**
     * Message id
     *
     * @param int $value
     * @return $this
     */
    public function setId($value)
    {
        return $this->_set(Message::ID, $value);
    }

    /**
     * Sender name
     *
     * @param string $value
     * @return $this
     */
    public function setSender($value)
    {
        return $this->_set(Message::SENDER, $value);
    }

    /**
     * Recipient name
     *
     * @param string $value
     * @return $this
     */
    public function setRecipient($value)
    {
        return $this->_set(Message::RECIPIENT, $value);
    }

    /**
     * Message text
     *
     * @param string $value
     * @return $this
     */
    public function setMessage($value)
    {
        return $this->_set(Message::MESSAGE, $value);
    }
}
