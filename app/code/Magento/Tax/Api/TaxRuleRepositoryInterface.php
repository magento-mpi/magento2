<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Api;

use \Magento\Tax\Api\Data\TaxRuleInterface;
use \Magento\Tax\Api\Data\TaxRuleSearchResultsInterface;

/**
 * previous implementation @see \Magento\Tax\Service\V1\TaxRuleServiceInterface
 */
interface TaxRuleRepositoryInterface
{
    /**
     * Save TaxRule
     *
     * @param TaxRuleInterface $rule
     * @return TaxRuleInterface $rule
     * @throws \Magento\Framework\Exception\InputException If input is invalid or required input is missing.
     * @throws \Exception If something went wrong while performing the update.
     */
    public function save(TaxRuleInterface $rule);

    /**
     * Delete TaxRule
     *
     * @param TaxRuleInterface $rule
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function delete(TaxRuleInterface $rule);

    /**
     * Delete TaxRule
     *
     * @param int $ruleId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If no TaxRate with the given ID can be found.
     * @throws \Exception If something went wrong while performing the delete.
     */
    public function deleteByIdentifier($ruleId);

    /**
     * Get TaxRule
     *
     * @param int $ruleId
     * @return TaxRuleInterface
     */
    public function get($ruleId);

    /**
     * Search TaxRules
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @return TaxRuleSearchResultsInterface containing TaxRuleInterface objects
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
