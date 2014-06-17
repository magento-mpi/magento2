<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\Converter;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Model\ClassModelFactory as TaxClassModelFactory;
use Magento\Tax\Model\Resource\TaxClass\Collection as TaxClassCollection;
use Magento\Tax\Model\Resource\TaxClass\CollectionFactory as TaxClassCollectionFactory;
use Magento\Tax\Service\V1\Data\SearchResultsBuilder;
use Magento\Tax\Service\V1\Data\TaxClass;

/**
 * Tax class service.
 */
class TaxClassService implements TaxClassServiceInterface
{
    /**
     * @var TaxClassCollectionFactory
     */
    protected $taxClassCollectionFactory;

    /**
     * @var SearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var TaxClassModelFactory
     */
    protected $taxClassModelFactory;

    /**
     * Initialize dependencies.
     *
     * @param TaxClassCollectionFactory $taxClassCollectionFactory
     * @param TaxClassModelFactory $taxClassModelFactory
     * @param SearchResultsBuilder $searchResultsBuilder
     * @param Converter $converter
     */
    public function __construct(
        TaxClassCollectionFactory $taxClassCollectionFactory,
        TaxClassModelFactory $taxClassModelFactory,
        SearchResultsBuilder $searchResultsBuilder,
        Converter $converter
    ) {
        $this->taxClassCollectionFactory = $taxClassCollectionFactory;
        $this->taxClassModelFactory = $taxClassModelFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxClass(TaxClass $taxClass)
    {
        if (is_null($taxClass->getId())) {
            throw InputException::invalidFieldValue('id', $taxClass->getId());
        }

        $originalTaxClassModel = $this->taxClassModelFactory->create()->load($taxClass->getId());
        $taxClassModel = $this->converter->createTaxClassModel($taxClass);

        /* should not be allowed to switch the tax class type */
        if ($originalTaxClassModel->getClassType() !== $taxClassModel->getClassType()) {
            throw InputException::invalidFieldValue('type', $taxClass->getType());
        }

        $this->validate($taxClassModel);
        $taxClassModel->save();

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxClass($taxClassId)
    {
        $taxClassModel = $this->taxClassModelFactory->create()->load($taxClassId);
        if (is_null($taxClassModel->getId())) {
            throw NoSuchEntityException::singleField('taxClassId', $taxClassId);
        }
        $taxClassModel->delete();

        return true;
    }

    /**
     * Validate tax class attribute values.
     *
     * @param TaxClassModel $taxClassModel
     * @throws InputException
     * @return void
     */
    private function validate(TaxClassModel $taxClassModel)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is(trim($taxClassModel->getId()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'id']);
        }

        if (!\Zend_Validate::is(trim($taxClassModel->getClassName()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'className']);
        }

        $classType = $taxClassModel->getClassType();

        if (!\Zend_Validate::is($classType, 'NotEmpty') &&
            $classType === TaxClassModel::TAX_CLASS_TYPE_CUSTOMER ||
            $classType === TaxClassModel::TAX_CLASS_TYPE_PRODUCT) {
            $exception->addError(
                InputException::INVALID_FIELD_VALUE,
                ['fieldName' => 'classType', 'value' => $taxClassModel->getClassType()]
            );
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function searchTaxClass(SearchCriteria $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);
        /** @var TaxClassCollection $collection */
        $collection = $this->taxClassCollectionFactory->create();
        /** TODO: This method duplicates functionality of search methods in other services and should be refactored. */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $taxClasses = [];
        /** @var \Magento\Tax\Model\ClassModel $taxClassModel */
        foreach ($collection as $taxClassModel) {
            $taxClasses[] = $this->converter->createTaxClassData($taxClassModel);
        }
        $this->searchResultsBuilder->setItems($taxClasses);
        return $this->searchResultsBuilder->create();
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * TODO: This method duplicates functionality of search methods in other services and should be refactored.
     *
     * @param FilterGroup $filterGroup
     * @param TaxClassCollection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(FilterGroup $filterGroup, TaxClassCollection $collection)
    {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = array('attribute' => $filter->getField(), $condition => $filter->getValue());
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
