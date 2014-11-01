<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Service\V1\Data\TaxDetails;

use Magento\Framework\Api\AttributeValueBuilder;
use Magento\Framework\Api\MetadataServiceInterface;

/**
 * Builder for the AppliedTax Service Data Object
 *
 * @method AppliedTax create()
 */
class AppliedTaxBuilder extends \Magento\Framework\Api\AbstractExtensibleObjectBuilder
{
    /**
     * AppliedTaxRate builder
     *
     * @var AppliedTaxRateBuilder
     */
    protected $appliedTaxRateBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Framework\Api\ObjectFactory $objectFactory
     * @param AttributeValueBuilder $valueBuilder
     * @param MetadataServiceInterface $metadataService
     * @param AppliedTaxRateBuilder $appliedTaxRateBuilder
     */
    public function __construct(
        \Magento\Framework\Api\ObjectFactory $objectFactory,
        AttributeValueBuilder $valueBuilder,
        MetadataServiceInterface $metadataService,
        AppliedTaxRateBuilder $appliedTaxRateBuilder
    ) {
        parent::__construct($objectFactory, $valueBuilder, $metadataService);
        $this->appliedTaxRateBuilder = $appliedTaxRateBuilder;
    }

    /**
     * Convenience method that returns AppliedTaxRateBuilder
     *
     * @return AppliedTaxRateBuilder
     */
    public function getAppliedTaxRateBuilder()
    {
        return $this->appliedTaxRateBuilder;
    }

    /**
     * Set tax rate key
     *
     * @param string $key
     * @return $this
     */
    public function setTaxRateKey($key)
    {
        return $this->_set(AppliedTax::KEY_TAX_RATE_KEY, $key);
    }

    /**
     * Set percent
     *
     * @param float $percent
     * @return $this
     */
    public function setPercent($percent)
    {
        return $this->_set(AppliedTax::KEY_PERCENT, $percent);
    }

    /**
     * Set amount
     *
     * @param float $amount
     * @return $this
     */
    public function setAmount($amount)
    {
        return $this->_set(AppliedTax::KEY_AMOUNT, $amount);
    }

    /**
     * Set rates
     *
     * @param AppliedTaxRate[] $rates
     * @return $this
     */
    public function setRates($rates)
    {
        return $this->_set(AppliedTax::KEY_RATES, $rates);
    }

    /**
     * {@inheritdoc}
     */
    protected function _setDataValues(array $data)
    {
        if (array_key_exists(AppliedTax::KEY_RATES, $data)) {
            $rates = [];
            foreach ($data[AppliedTax::KEY_RATES] as $rateArray) {
                $rates[] = $this->appliedTaxRateBuilder->populateWithArray($rateArray)->create();
            }
            $data[AppliedTax::KEY_RATES] = $rates;
        }
        return parent::_setDataValues($data);
    }
}
