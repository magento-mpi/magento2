<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Service\V1\Cart;

interface TotalsServiceInterface
{
    /**
     * Retrieve quote totals data
     *
     * @param int $cartId
     * @return \Magento\Checkout\Service\V1\Data\Cart\Totals
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getTotals($cartId);
}
