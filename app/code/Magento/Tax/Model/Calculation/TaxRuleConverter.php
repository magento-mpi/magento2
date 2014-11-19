<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation\Rule as TaxRuleModel;
use Magento\Tax\Model\Calculation\RuleFactory as TaxRuleModelFactory;
use Magento\Tax\Api\Data\TaxRuleInterface as TaxRuleDataObject;
use Magento\Tax\Api\Data\TaxRuleDataBuilder;

/**
 * Tax Rule Model converter.
 *
 * Converts a Tax Rule Model to a Data Object or vice versa.
 */
class TaxRuleConverter
{
    /**
     * @var TaxRuleDataBuilder
     */
    protected $taxRuleDataObjectBuilder;

    /**
     * @var TaxRuleModelFactory
     */
    protected $taxRuleModelFactory;

    /**
     * @param TaxRuleDataBuilder $taxRuleDataObjectBuilder
     * @param TaxRuleModelFactory $taxRuleModelFactory
     */
    public function __construct(
        TaxRuleDataBuilder $taxRuleDataObjectBuilder,
        TaxRuleModelFactory $taxRuleModelFactory
    ) {
        $this->taxRuleDataObjectBuilder = $taxRuleDataObjectBuilder;
        $this->taxRuleModelFactory = $taxRuleModelFactory;
    }

    /**
     * Convert a tax rule data object to tax rule model
     *
     * @param TaxRuleDataObject $taxRule
     * @return TaxRuleModel
     */
    public function createTaxRuleModel(TaxRuleDataObject $taxRuleDataObject)
    {
        $taxRuleModel = $this->taxRuleModelFactory->create();
        $ruleId = $taxRuleDataObject->getId();
        if ($ruleId) {
            $taxRuleModel->setId($ruleId);
        }
        $taxRuleModel->setTaxCustomerClass($taxRuleDataObject->getCustomerTaxClassIds());
        $taxRuleModel->setTaxProductClass($taxRuleDataObject->getProductTaxClassIds());
        $taxRuleModel->setTaxRate($taxRuleDataObject->getTaxRateIds());
        $taxRuleModel->setCode($taxRuleDataObject->getCode());
        $taxRuleModel->setPriority($taxRuleDataObject->getPriority());
        $taxRuleModel->setPosition($taxRuleDataObject->getSortOrder());
        $taxRuleModel->setCalculateSubtotal($taxRuleDataObject->getCalculateSubtotal());
        return $taxRuleModel;
    }
}
