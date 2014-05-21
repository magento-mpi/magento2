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

class TaxRuleService implements TaxRuleServiceInterface
{

    public function __construct()
    {
    }

    /**
     * @inheritdoc
     */
    public function createTaxRule(TaxRule $rule)
    {
        // TODO: Implement createTaxRule() method.
    }

    /**
     * @inheritdoc
     */
    public function updateTaxRule(TaxRule $rule)
    {
        // TODO: Implement updateTaxRule() method.
    }

    /**
     * @inheritdoc
     */
    public function deleteTaxRule($ruleId)
    {
        // TODO: Implement deleteTaxRule() method.
    }

    /**
     * @inheritdoc
     */
    public function getTaxRule($ruleId)
    {
        // TODO: Implement getTaxRule() method.
    }

    /**
     * @inheritdoc
     */
    public function getTaxCalculationPreference($storeId)
    {
        // TODO: Implement getTaxCalculationPreference() method.
    }

    /**
     * @inheritdoc
     */
    public function setTaxCalculationPreference($storeId, array $preferences)
    {
        // TODO: Implement setTaxCalculationPreference() method.
    }

    /**
     * @inheritdoc
     */
    public function searchTaxRules(SearchCriteria $searchCriteria)
    {
        // TODO: Implement searchTaxRules() method.
    }
}
