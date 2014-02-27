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
     * @var \Magento\Mail\MessageInterface
     */
    protected $_message;

    /**
     * @param MessageInterface $message
     * @param null $parameters
     * @throws \InvalidArgumentException
     */
    public function __construct(\Magento\Mail\MessageInterface $message, $parameters = null)
    {
        if (!$message instanceof \Zend_Mail) {
            throw new \InvalidArgumentException('The message should be an instance of \Zend_Mail');
        }
        parent::__construct($parameters);
        $this->_message = $message;
    }

    /**
     * Send a mail using this transport
     *
     * @throws \Magento\Mail\Exception
     */
    public function sendMessage()
    {
        try {
            parent::send($this->_message);
        } catch (\Exception $e) {
            throw new \Magento\Mail\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
