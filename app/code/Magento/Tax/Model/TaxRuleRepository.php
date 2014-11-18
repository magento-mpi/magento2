<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model;

use \Magento\Tax\Api\Data\TaxRuleInterface;
use \Magento\Tax\Api\TaxRuleRepositoryInterface;
use \Magento\Tax\Model\Calculation\TaxRuleConverter;
use \Magento\Tax\Model\Calculation\TaxRuleRegistry;

class TaxRuleRepository implements TaxRuleRepositoryInterface
{
    /**
     * @var TaxRuleRegistry
     */
    protected $taxRuleRegistry;

    /**
     * @var TaxRuleConverter
     */
    protected $converter;

    public function __construct(TaxRuleRegistry $taxRuleRegistry, TaxRuleConverter $taxRuleConverter)
    {
        $this->taxRuleRegistry = $taxRuleRegistry;
        $this->converter = $taxRuleConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function get($ruleId)
    {
        $taxRuleModel = $this->taxRuleRegistry->retrieveTaxRule($ruleId);
        return $this->converter->createTaxRuleDataObjectFromModel($taxRuleModel);
    }

    /**
     * {@inheritdoc}
     */
    public function save(TaxRuleInterface $rule)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function delete(TaxRuleInterface $rule)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIdentifier($ruleId)
    {

    }

    /**
     * {@inheritdoc}
     */
    public function search(\Magento\Framework\Api\SearchCriteria $searchCriteria)
    {

    }
}
