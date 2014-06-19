<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Tax\Model\Calculation\Rate\Converter;
use Magento\Tax\Model\Calculation\RateFactory;
use Magento\Tax\Service\V1\Data\TaxRate as TaxRateDataObject;
use Magento\Tax\Model\Calculation\Rate as RateModel;
use Magento\Tax\Service\V1\Data\TaxRateBuilder;
use Magento\Framework\Exception\InputException;
use Magento\Tax\Model\Calculation\RateRegistry;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\Resource\Calculation\Rate\Collection;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;

/**
 * Handles tax rate CRUD operations
 *
 */
class TaxRateService implements TaxRateServiceInterface
{
    /**
     * Tax rate model and tax rate data object converter
     *
     * @var  Converter
     */
    protected $converter;

    /**
     * Tax rate data object builder
     *
     * @var  TaxRateBuilder
     */
    protected $rateBuilder;

    /**
     * Tax rate registry
     *
     * @var  RateRegistry
     */
    protected $rateRegistry;

    /**
     * @var Data\TaxRateSearchResultsBuilder
     */
    private $taxRateSearchResultsBuilder;

    /**
     * @var RateFactory
     */
    private $rateFactory;

    /**
     *@var Data\TaxRateBuilder
     */
    private $taxRateBuilder;

    /**
     * Constructor
     *
     * @param TaxRateBuilder $rateBuilder
     * @param Converter $converter
     * @param RateRegistry $rateRegistry
     * @param Data\TaxRateSearchResultsBuilder $taxRateSearchResultsBuilder
     * @param RateFactory $rateFactory
     * @param Data\TaxRateBuilder $taxRateBuilder
     */
    public function __construct(
        TaxRateBuilder $rateBuilder,
        Converter $converter,
        RateRegistry $rateRegistry,
        Data\TaxRateSearchResultsBuilder $taxRateSearchResultsBuilder,
        RateFactory $rateFactory,
        Data\TaxRateBuilder $taxRateBuilder

    ) {
        $this->rateBuilder = $rateBuilder;
        $this->converter = $converter;
        $this->rateRegistry = $rateRegistry;
        $this->taxRateSearchResultsBuilder = $taxRateSearchResultsBuilder;
        $this->rateFactory = $rateFactory;
        $this->taxRateBuilder = $taxRateBuilder;
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
    public function getTaxRate($rateId)
    {
        $rateModel = $this->rateRegistry->retrieveTaxRate($rateId);
        return $this->converter->createTaxRateDataObjectFromModel($rateModel);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxRate(TaxRateDataObject $taxRate)
    {
        // Only update existing tax rates
        $this->rateRegistry->retrieveTaxRate($taxRate->getId());

        $this->saveTaxRate($taxRate);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxRate($rateId)
    {
        $rateModel = $this->rateRegistry->retrieveTaxRate($rateId);
        $rateModel->delete();
        $this->rateRegistry->removeTaxRate($rateId);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function searchTaxRates(SearchCriteria $searchCriteria)
    {
        $collection = $this->rateFactory->create()->getCollection();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($sortOrders as $field => $direction) {
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $taxRate = [];

        /** @var \Magento\Tax\Model\Calculation\Rate $taxRateModel */
        foreach ($collection as $taxRateModel) {
            $taxRate[] = $this->converter->createTaxRateDataObjectFromModel($taxRateModel);
        }

        return $this->taxRateSearchResultsBuilder
            ->setItems($taxRate)
            ->setTotalCount($collection->getSize())
            ->setSearchCriteria($searchCriteria)
            ->create();
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
        $this->rateRegistry->registerTaxRate($taxRateModel);
        return $taxRateModel;
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, Collection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $this->translateField($filter->getField());
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }

    /**
     * Translates a field name to a DB column name for use in collection queries.
     *
     * @param string $field a field name that should be translated to a DB column name.
     * @return string
     */
    protected function translateField($field)
    {
        switch ($field) {
            case TaxRateDataObject::KEY_POSTCODE:
            case TaxRateDataObject::KEY_COUNTRY_ID:
            case TaxRateDataObject::KEY_REGION_ID:
                return 'tax_' . $field;
            case TaxRateDataObject::KEY_PERCENTAGE_RATE:
                return 'rate';
            default:
                return $field;
        }
    }

    /**
     * Validate tax rate
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

        if ($taxRate->getZipRange()) {
            $zipRangeFromTo = [
                'zip_from' => $taxRate->getZipRange()->getFrom(),
                'zip_to' => $taxRate->getZipRange()->getTo()
            ];
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
