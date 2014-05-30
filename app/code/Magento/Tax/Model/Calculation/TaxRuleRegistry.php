<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

use Magento\Tax\Model\Calculation\RuleFactory as TaxRuleModelFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Tax\Model\Calculation\Rule as TaxRuleModel;

class TaxRuleRegistry
{
    /** @var  TaxRuleModelFactory */
    private $taxModelRuleFactory;

    /**
     * @var array
     */
    private $registry = [];

    /**
     * Constructor
     *
     * @param TaxRuleModelFactory $taxModelRuleFactory
     */
    public function __construct(
        TaxRuleModelFactory $taxModelRuleFactory
    ) {
        $this->taxModelRuleFactory = $taxModelRuleFactory;
    }

    /**
     * Registers TaxRule Model to registry
     *
     * @param TaxReModel $taxRuleModel
     */
    public function registerTaxRule(TaxRuleModel $taxRuleModel)
    {
        $this->registry[$taxRuleModel->getId()] = $taxRuleModel;
    }

    /**
     * Retrieve TaxRule Model from registry given an id
     *
     * @param int $taxRuleId
     * @return TaxRuleModel
     * @throws NoSuchEntityException
     */
    public function retrieveTaxRule($taxRuleId)
    {
        if (isset($this->registry[$taxRuleId])) {
            return $this->registry[$taxRuleId];
        }
        /** @var TaxRuleModel $taxRuleModel */
        $taxRuleModel = $this->taxModelRuleFactory->create()->load($taxRuleId);
        if (!$taxRuleModel->getId()) {
            // tax rule does not exist
            throw NoSuchEntityException::singleField('taxRuleId', $taxRuleId);
        }
        $this->registry[$taxRuleModel->getId()] = $taxRuleModel;
        return $taxRuleModel;
    }
}
