<?php
/**
 * Mail Message
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail;

class Message extends \Zend_Mail implements MessageInterface
{
    /**
     * Message type
     *
     * @var string
     */
    protected $messageType = self::TYPE_TEXT;

    /**
     * Set message body
     *
     * @param string $body
     * @return $this
     */
    public function setBody($body)
    {
        return $this->messageType == self::TYPE_TEXT ? $this->setBodyText($body) : $this->setBodyHtml($body);
    }

    /**
     * Set message body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->messageType == self::TYPE_TEXT ? $this->getBodyText() : $this->getBodyHtml();
    }

    /**
     * Set message type
     *
     * @param string $type
     * @return $this
     */
    public function setMessageType($type)
    {
        $this->messageType = $type;
        return $this;
    }
}
