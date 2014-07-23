<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Cart;

interface WriteServiceInterface
{
    /**
     * Create empty cart/quote for anonymous customer
     *
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return int cart id
     */
    public function create();
}

