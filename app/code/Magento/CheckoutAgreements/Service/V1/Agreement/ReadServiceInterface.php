<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CheckoutAgreements\Service\V1\Agreement;

interface ReadServiceInterface
{
    /**
     * Retrieve the list of active checkout agreements
     *
     * @return \Magento\CheckoutAgreements\Service\V1\Data\Agreement[]
     */
    public function getList();
}
