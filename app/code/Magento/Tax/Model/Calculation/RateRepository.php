<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\Calculation;

use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Model\Exception as ModelException;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria;
use Magento\Tax\Model\Calculation\Rate\Converter;
use Magento\Tax\Model\Resource\Calculation\Rate\Collection;
use Magento\Tax\Api\Data\TaxRateInterface as TaxRateDataObject;
use Magento\Framework\Api\SortOrder;

class RateRepository implements \Magento\Tax\Api\TaxRateRepositoryInterface
{
    const MESSAGE_TAX_RATE_ID_IS_NOT_ALLOWED = 'id is not expected for this request.';

    /**
     * Tax rate model and tax rate data object converter
     *
     * @var  Converter
     */
    protected $converter;

    /**
     * Tax rate data object builder
     *
     * @var  \Magento\Tax\Api\Data\TaxRateDataBuilder
     */
    protected $rateBuilder;

    /**
     * Tax rate registry
     *
     * @var  RateRegistry
     */
    protected $rateRegistry;

    /**
     * @var \Magento\Tax\Api\Data\TaxRuleSearchResultsDataBuilder
     */
    private $taxRateSearchResultsBuilder;

    /**
     * @var RateFactory
     */
    private $rateFactory;

    /**
     * @var \Magento\Directory\Model\CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;

    /**
     * @var \Magento\Tax\Model\Resource\Calculation\Rate
     */
    protected $resourceModel;

    /**
     * Constructor
     *
     * @param \Magento\Tax\Api\Data\TaxRateDataBuilder $rateBuilder
     * @param Converter $converter
     * @param RateRegistry $rateRegistry
     * @param \Magento\Tax\Api\Data\TaxRuleSearchResultsDataBuilder $taxRateSearchResultsBuilder
     * @param RateFactory $rateFactory
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Tax\Api\Data\TaxRateDataBuilder $rateBuilder,
        Converter $converter,
        RateRegistry $rateRegistry,
        \Magento\Tax\Api\Data\TaxRuleSearchResultsDataBuilder $taxRateSearchResultsBuilder,
        RateFactory $rateFactory,
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        \Magento\Tax\Model\Resource\Calculation\Rate $rateResource
    ) {
        $this->rateBuilder = $rateBuilder;
        $this->converter = $converter;
        $this->rateRegistry = $rateRegistry;
        $this->taxRateSearchResultsBuilder = $taxRateSearchResultsBuilder;
        $this->rateFactory = $rateFactory;
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->resourceModel = $rateResource;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Tax\Api\Data\TaxRateInterface $taxRate)
    {
        if ($taxRate->getTaxCalculationRateId()) {
            $this->rateRegistry->retrieveTaxRate($taxRate->getTaxCalculationRateId());
        }
        $this->validate($taxRate);
        $taxRateTitles = $taxRate->getTitles();
        try {
            $this->resourceModel->save($taxRate);
            $taxRate->saveTitles($taxRateTitles);
        } catch (ModelException $e) {
            if ($e->getCode() == ModelException::ERROR_CODE_ENTITY_ALREADY_EXISTS) {
                throw new InputException($e->getMessage());
            } else {
                throw $e;
            }
        }
        $this->rateRegistry->registerTaxRate($taxRate);
        return $taxRate;
    }

    /**
     * {@inheritdoc}
     */
    public function get($rateId)
    {
        return $this->rateRegistry->retrieveTaxRate($rateId);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(\Magento\Tax\Api\Data\TaxRateInterface $taxRate)
    {
        return $this->resourceModel->delete($taxRate);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByIdentifier($rateId)
    {
        $rateModel = $this->rateRegistry->retrieveTaxRate($rateId);
        $this->delete($rateModel);
        $this->rateRegistry->removeTaxRate($rateId);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Magento\Tax\Model\Resource\Calculation\Rate\Collection $collection */
        $collection = $this->rateFactory->create()->getCollection();
        $collection->joinRegionTable();

        //Add filters from root filter group to the collection
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }

        $sortOrders = $searchCriteria->getSortOrders();
        /** @var SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $this->translateField($sortOrder->getField()),
                    ($sortOrder->getDirection() == SearchCriteria::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        $taxRate = [];

        /** @var \Magento\Tax\Model\Calculation\Rate $taxRateModel */
        foreach ($collection as $taxRateModel) {
            $taxRate[] = $taxRateModel;
        }

        return $this->taxRateSearchResultsBuilder
            ->setItems($taxRate)
            ->setTotalCount($collection->getSize())
            ->setSearchCriteria($searchCriteria)
            ->create();
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
            case TaxRateDataObject::KEY_REGION_NAME:
                return 'region_table.code';
            default:
                return "main_table." . $field;
        }
    }

    /**
     * Validate tax rate
     *
     * @param \Magento\Tax\Api\Data\TaxRateInterface $taxRate
     * @throws InputException
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    private function validate(\Magento\Tax\Api\Data\TaxRateInterface $taxRate)
    {
        $exception = new InputException();

        $countryCode = $taxRate->getTaxCountryId();
        if (!\Zend_Validate::is($countryCode, 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'country_id']);
        } else if (!\Zend_Validate::is($this->countryFactory->create()->loadByCode($countryCode)->getId(),
            'NotEmpty')
        ) {
            $exception->addError(
                InputException::INVALID_FIELD_VALUE, ['fieldName' => 'country_id', 'value' => $countryCode]
            );
        }

        $regionCode = $taxRate->getTaxRegionId();
        // if regionCode eq 0 (all regions *), do not validate with existing region list
        if (\Zend_Validate::is($regionCode, 'NotEmpty') &&
            ($regionCode != "0" &&
                !\Zend_Validate::is($this->regionFactory->create()->load($regionCode)->getId(), 'NotEmpty'))
        ) {
            $exception->addError(
                InputException::INVALID_FIELD_VALUE, ['fieldName' => 'region_id', 'value' => $regionCode]
            );
        }

        if (!\Zend_Validate::is($taxRate->getRate(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'percentage_rate']);
        }

        if (!\Zend_Validate::is(trim($taxRate->getCode()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'code']);
        }

        if ($taxRate->getZipIsRange()) {
            $zipRangeFromTo = [
                'zip_from' => $taxRate->getZipFrom(),
                'zip_to' => $taxRate->getZipTo()
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
            if (!\Zend_Validate::is(trim($taxRate->getTaxPostcode()), 'NotEmpty')) {
                $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'postcode']);
            }
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }
}
