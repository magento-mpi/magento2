<?php
/**
 * Mail Message interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail;

interface MessageInterface
{
    /**
     * Types of message
     */
    const TYPE_TEXT = 'text/plain';
    const TYPE_HTML = 'text/html';

    /**
     * Set message subject
     *
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject);

    /**
     * Get message subject
     *
     * @return string
     */
    public function getSubject();

    /**
     * Set message body
     *
     * @param mixed $body
     * @return $this
     */
    public function setBody($body);

    /**
     * Get message body
     *
     * @return mixed
     */
    public function getBody();

    /**
     * Set from address
     *
     * @param string|array $fromAddress
     * @return $this
     */
    public function setFrom($fromAddress);

    /**
     * Set to address
     *
     * @param string|array $toAddress
     * @return $this
     */
    public function setTo($toAddress);

    /**
     * Add to address
     *
     * @param string|array $toAddress
     * @return $this
     */
    public function addTo($toAddress);

    /**
     * Set cc address
     * @param string|array $ccAddress
     * @return $this
     */
    public function setCc($ccAddress);

    /**
     * Add cc address
     *
     * @param string|array $ccAddress
     * @return $this
     */
    public function addCc($ccAddress);

    /**
     * Set bcc address
     *
     * @param string|array $bccAddress
     * @return $this
     */
    public function setBcc($bccAddress);

    /**
     * Add bcc address
     *
     * @param string|array $bccAddress
     * @return $this
     */
    public function addBcc($bccAddress);

    /**
     * Set reply-to address
     *
     * @param string|array $replyToAddress
     * @return $this
     */
    public function setReplyTo($replyToAddress);

    /**
     * Add reply-to address
     *
     * @param string|array $replyToAddress
     * @return $this
     */
    public function addReplyTo($replyToAddress);

    /**
     * Set message type
     *
     * @param string $type
     * @return $this
     */
    public function setMessageType($type);
}
