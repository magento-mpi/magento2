<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api;

use \Magento\Tax\Api\Data\TaxRuleInterface;

/**
 * previous implementation @see \Magento\Tax\Service\V1\TaxRuleServiceInterface::getRatesByCustomerAndProductTaxClassId
 */
interface TaxRuleManagementInterface
{
    /**
     * Get rates by customerTaxClassId and productTaxClassId
     *
     * @param int $customerTaxClassId
     * @param int $productTaxClassId
     * @return TaxRuleInterface[]
     */
    public function getRatesByCustomerAndProductTaxClassId($customerTaxClassId, $productTaxClassId);
}
