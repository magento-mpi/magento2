<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Service\V1\Data\TaxRule;

/**
 * Interface TaxRuleServiceInterface
 */
interface TaxRuleServiceInterface
{
    /**
     * Create TaxRule
     *
     * @param TaxRule $rule
     * @return TaxRule
     */
    public function createTaxRule(TaxRule $rule);

    /**
     * Update TaxRule
     *
     * @param TaxRule $rule
     * @return TaxRule
     */
    public function updateTaxRule(TaxRule $rule);

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
     * @return TaxRule
     */
    public function getTaxRule($ruleId);

    /**
     * Get TaxRule calculation preferences
     *
     * @param int $storeId
     * @return array
     */
    public function getTaxCalculationPreference($storeId);

    /**
     * Set TaxRule calculation preferences
     *
     * @param int $storeId
     * @param array
     */
    public function setTaxCalculationPreference($storeId, array $preferences);

    /**
     * Search TaxRules
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @return \Magento\Tax\Service\V1\Data\SearchResults containing Data\TaxRule objects
     */
    public function searchTaxRules(SearchCriteria $searchCriteria);
}
