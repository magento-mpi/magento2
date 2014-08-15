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
class Message extends \Magento\Framework\Service\Data\AbstractObject
{
    const ID = 'id';

    const SENDER = 'sender';

    const RECIPIENT = 'recipient';

    const MESSAGE = 'message';

    /**
     *
     *
     * @return int
     */
    public function getId()
    {
        return $this->_get(self::ID);
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
