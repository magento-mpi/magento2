<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\Exception as ModelException;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\ClassModel as TaxClassModel;
use Magento\Tax\Model\ClassModelFactory as TaxClassModelFactory;
use Magento\Tax\Model\Converter;
use Magento\Tax\Model\Resource\TaxClass\Collection as TaxClassCollection;
use Magento\Tax\Model\Resource\TaxClass\CollectionFactory as TaxClassCollectionFactory;
use Magento\Tax\Service\V1\Data\SearchResultsBuilder;
use Magento\Tax\Service\V1\Data\TaxClass as TaxClassDataObject;

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
    public function createTaxClass(TaxClassDataObject $taxClass)
    {
        $this->validateTaxClassData($taxClass);
        $taxModel = $this->converter->createTaxClassModel($taxClass);
        try {
            $taxModel->save();
        } catch (ModelException $e) {
            throw new InputException('A class with the same name already exists for ClassType %classType.',
                ['classType' => $taxClass->getClassType()]);
        }
        return $taxModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxClass($taxClassId)
    {
        $taxClassModel = $this->taxClassModelFactory->create()->load($taxClassId);
        if (!$taxClassModel->getId()) {
            throw NoSuchEntityException::singleField('taxClassId', $taxClassId);
        }
        return $this->converter->createTaxClassData($taxClassModel);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxClass($taxClassId, TaxClassDataObject $taxClass)
    {
        $this->validateTaxClassData($taxClass);

        if (!$taxClassId) {
            throw InputException::invalidFieldValue('taxClassId', $taxClassId);
        }

        if ($taxClass->getClassId() && ($taxClassId != $taxClass->getClassId())) {
            throw InputException::invalidFieldValue('classId', $taxClass->getClassId());
        }

        $originalTaxClassModel = $this->taxClassModelFactory->create()->load($taxClassId);
        if (!$originalTaxClassModel->getId()) {
            throw NoSuchEntityException::singleField('taxClassId', $taxClassId);
        }

        $taxClassModel = $this->converter->createTaxClassModel($taxClass);
        $taxClassModel->setId($taxClassId);

        /* should not be allowed to switch the tax class type */
        if ($originalTaxClassModel->getClassType() !== $taxClassModel->getClassType()) {
            throw InputException::invalidFieldValue('type', $taxClass->getClassType());
        }

        try {
            $taxClassModel->save();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxClass($taxClassId)
    {
        $taxClassModel = $this->taxClassModelFactory->create()->load($taxClassId);
        if (!$taxClassModel->getId()) {
            throw NoSuchEntityException::singleField('taxClassId', $taxClassId);
        }

        try {
            $taxClassModel->delete();
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Validate TaxClass Data
     *
     * @param TaxClassDataObject $taxClass
     * @throws InputException
     */
    protected function validateTaxClassData(TaxClassDataObject $taxClass)
    {
        $exception = new InputException();

        if (!\Zend_Validate::is(trim($taxClass->getClassName()), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxClassDataObject::KEY_NAME]);
        }

        $classType = $taxClass->getClassType();
        if (!\Zend_Validate::is(trim($classType), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => TaxClassDataObject::KEY_TYPE]);
        } else if ($classType !== TaxClassDataObject::TYPE_CUSTOMER
            && $classType !== TaxClassDataObject::TYPE_PRODUCT
        ) {
            $exception->addError(
                InputException::INVALID_FIELD_VALUE,
                ['fieldName' => TaxClassDataObject::KEY_TYPE, 'value' => $classType]
            );
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Retrieve tax classes which match a specific criteria.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     * @return \Magento\Tax\Service\V1\Data\SearchResults containing Data\TaxClass
     * @throws \Magento\Framework\Exception\InputException
     */
    public function searchTaxClass(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria)
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
        foreach ($collection->getItems() as $taxClassModel) {
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
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
