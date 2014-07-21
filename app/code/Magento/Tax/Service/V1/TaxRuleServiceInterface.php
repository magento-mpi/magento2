<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

/**
 * Interface TaxRuleServiceInterface
 */
interface TaxRuleServiceInterface
{
    /**
     * Create TaxRule
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRule $rule
     * @return \Magento\Tax\Service\V1\Data\TaxRule
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Exception If something went wrong while performing the update.
     */
    public function createTaxRule(\Magento\Tax\Service\V1\Data\TaxRule $rule);

    /**
     * Update TaxRule
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRule $rule
     * @return bool
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Magento\Framework\Exception\NoSuchEntityException If the TaxRule to update can't be found in the system.
     * @throws \Exception If something went wrong while performing the update.
     */
    public function updateTaxRule(\Magento\Tax\Service\V1\Data\TaxRule $rule);

    /**
     * Delete TaxRule
     *
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function deleteTaxRule($ruleId);

    /**
     * Get TaxRule
     *
     * @param int $ruleId
     * @return \Magento\Tax\Service\V1\Data\TaxRule
     */
    public function getTaxRule($ruleId);

    /**
     * Search TaxRules
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\TaxRuleSearchResults containing Data\TaxRule objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function searchTaxRules(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Get rates by customerTaxClassId and productTaxClassId
     *
     * @param int $customerTaxClassId
     * @param int $productTaxClassId
     * @return \Magento\Tax\Service\V1\Data\TaxRate[]
     */
    public function getRatesByCustomerAndProductTaxClassId($customerTaxClassId, $productTaxClassId);
}
