<?php
/**
 * Mail Transport interface
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Mail;

interface TransportInterface
{
    /**
     * Send a mail using this transport
     *
     * @throws \Magento\Mail\Exception
     */
    public function sendMessage();
}
