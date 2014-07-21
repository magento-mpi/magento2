<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Items;

interface ReadServiceInterface
{
    /**
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Items[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function itemsList($cartId);
}
