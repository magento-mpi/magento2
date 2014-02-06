<?php
/**
 * Mail Transport
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Mail;

class Transport extends \Zend_Mail_Transport_Sendmail implements \Magento\Mail\TransportInterface
{
    /**
     * Send a mail using this transport
     *
     * @param \Magento\Mail\MessageInterface $message
     * @throws \InvalidArgumentException
     */
    public function sendMessage(\Magento\Mail\MessageInterface $message)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }
        parent::send($message);
    }
}
