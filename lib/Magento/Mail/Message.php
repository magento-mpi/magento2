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
     * Body
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
     * Set to address
     *
     * @param string|array $toAddress
     * @return $this
     */
    public function setTo($toAddress)
    {
        return $this->addTo($toAddress);
    }

    /**
     * Set cc address
     * @param string|array $ccAddress
     * @return $this
     */
    public function setCc($ccAddress)
    {
        return $this->addCc($ccAddress);
    }

    /**
     * Set bcc address
     *
     * @param string|array $bccAddress
     * @return $this
     */
    public function setBcc($bccAddress)
    {
        return $this->addBcc($bccAddress);
    }

    /**
     * Set reply-to address
     *
     * @param string|array $replyToAddress
     * @return $this
     */
    public function addReplyTo($replyToAddress)
    {
        return $this->addBcc($replyToAddress);
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
