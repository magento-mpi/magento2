<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;


use Magento\Tax\Model\Calculation\Rate\Converter;
use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;
use Magento\Tax\Model\Calculation\RateFactory as TaxRateModelFactory;
use Magento\Tax\Model\Calculation\Rate as RateModel;
use Magento\Tax\Service\V1\Data\TaxRateBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Tax\Model\TaxRegistry;

/**
 * Handles tax rate CRUD operations
 *
 */
class TaxRateService implements TaxRateServiceInterface
{
    /**
     * @var  TaxRateModelFactory
     */
    protected $rateModelFactory;

    /**
     * @var  Converter
     */
    protected $converter;

    /**
     * @var  TaxRateBuilder
     */
    protected $rateBuilder;

    /**
     * @var  TaxRegistry
     */
    protected $taxRegistry;

    /**
     * Constructor
     *
     * @param TaxRateModelFactory $rateFactory
     * @param TaxRateBuilder $rateBuilder
     * @param Converter $converter
     * @param TaxRegistry $taxRegistry
     */
    public function __construct(
        TaxRateModelFactory $rateFactory,
        TaxRateBuilder $rateBuilder,
        Converter $converter,
        TaxRegistry $taxRegistry
    ) {
        $this->rateModelFactory = $rateFactory;
        $this->rateBuilder = $rateBuilder;
        $this->converter = $converter;
        $this->taxRegistry = $taxRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createTaxRate(TaxRateDataObject $taxRate)
    {
        $rateModel = $this->saveTaxRate($taxRate);
        return $this->converter->createTaxRateDataObjectFromModel($rateModel);
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxRates()
    {
        $collection = $this->rateModelFactory->create()->getResourceCollection();
        $taxRates = [];
        /** @var RateModel $rateModel */
        foreach ($collection as $rateModel) {
            $taxRates[] = $this->converter->createTaxRateDataObjectFromModel($rateModel);
        }
        return $taxRates;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxRate(TaxRateDataObject $taxRate)
    {
        // TODO: Implement updateTaxRate() method.
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxRate($rateId)
    {
        // TODO: Implement deleteTaxRate() method.
    }

    /**
     * Save Tax Rate
     *
     * @param TaxRateDataObject $taxRate
     * @throws InputException
     * @throws \Magento\Framework\Model\Exception
     * @return RateModel
     */
    protected function saveTaxRate(TaxRateDataObject $taxRate)
    {
        $this->validate($taxRate);
        $taxRateModel = $this->converter->createTaxRateModel($taxRate);
        $taxRateModel->save();
        $this->taxRegistry->registerTaxRate($taxRateModel);
        return $taxRateModel;
    }

    /**
     * Validate tax rate .
     *
     * @param TaxRateDataObject $taxRate
     * @throws InputException
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(TaxRateDataObject $taxRate)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($taxRate->getCountryId()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'country_id']);
        }
        if (!\Zend_Validate::is(trim($taxRate->getRegionId()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'region_id']);
        }
        if (!\Zend_Validate::is(trim($taxRate->getPercentageRate()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'percentage_rate']);
        }
        if (!\Zend_Validate::is(trim($taxRate->getCode()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'code']);
        }
        $zipRangeFromTo = [
            'zip_from' => $taxRate->getZipRange()->getFrom(),
            'zip_to' => $taxRate->getZipRange()->getTo()
        ];
        if (!empty($zipRangeFromTo['zip_from']) && !empty($zipRangeFromTo['zip_to'])) {
            foreach ($zipRangeFromTo as $key => $value) {
                if (!is_numeric($value) || $value < 0) {
                    $exception->addError(
                        InputException::INVALID_FIELD_VALUE,
                        ['fieldName' => $key, 'value' => $value]
                    );
                }
            }
            if ($zipRangeFromTo['zip_from'] > $zipRangeFromTo['zip_to']) {
                $exception->addError('Range To should be equal or greater than Range From.');
            }

        } else {
            if (!\Zend_Validate::is(trim($taxRate->getPostcode()), 'NotEmpty')) {
                $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'postcode']);
            }
        }
        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}
