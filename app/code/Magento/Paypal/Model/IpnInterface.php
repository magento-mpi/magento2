<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model;

interface IpnInterface
{
    /**
     * Get ipn data, send verification to PayPal, run corresponding handler
     *
     * @return void
     * @throws \Exception
     */
    public function processIpnRequest();
}
