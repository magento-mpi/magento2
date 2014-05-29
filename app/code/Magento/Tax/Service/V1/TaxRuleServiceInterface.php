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
     */
    public function createTaxRule(\Magento\Tax\Service\V1\Data\TaxRule $rule);

    /**
     * Update TaxRule
     *
     * @param \Magento\Tax\Service\V1\Data\TaxRule $rule
     * @return \Magento\Tax\Service\V1\Data\TaxRule
     */
    public function updateTaxRule(\Magento\Tax\Service\V1\Data\TaxRule $rule);

    /**
     * Delete TaxRule
     *
     * @param int $ruleId
     * @return bool
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
     * @return bool
     */
    public function getCalculateOffSubtotalOnly();

    /**
     * Search TaxRules
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @return \Magento\Tax\Service\V1\Data\TaxRuleSearchResults containing Data\TaxRule objects
     */
    public function searchTaxRules(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);
}
