<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\PaymentMethod;

class WriteService implements WriteServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function add(\Magento\Checkout\Service\V1\Data\Cart\PaymentMethod $method, $cartId)
    {
        return $cartId;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($methodId, $cartId)
    {
        return true;
    }
}
