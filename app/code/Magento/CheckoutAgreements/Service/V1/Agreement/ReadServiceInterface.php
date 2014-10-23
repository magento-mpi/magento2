<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Service\V1\Agreement;

/**
 * Checkout agreement service interface.
 */
interface ReadServiceInterface
{
    /**
     * Lists active checkout agreements.
     *
     * @return \Magento\CheckoutAgreements\Service\V1\Data\Agreement[] Array of active checkout agreements.
     */
    public function getList();
}
