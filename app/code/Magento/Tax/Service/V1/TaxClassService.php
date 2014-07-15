<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Service\V1;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Model\Exception as ModelException;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Framework\Service\V1\Data\SearchCriteria;
use Magento\Tax\Model\ClassModelRegistry;
use Magento\Tax\Model\Converter;
use Magento\Tax\Model\Resource\TaxClass\Collection as TaxClassCollection;
use Magento\Tax\Model\Resource\TaxClass\CollectionFactory as TaxClassCollectionFactory;
use Magento\Tax\Service\V1\Data\TaxClassSearchResultsBuilder;
use Magento\Tax\Service\V1\Data\TaxClass as TaxClassDataObject;
use Magento\Framework\Exception\LocalizedException;

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
     * @var TaxClassSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var ClassModelRegistry
     */
    protected $classModelRegistry;

    const CLASS_ID_NOT_ALLOWED = 'class_id is not expected for this request.';

    /**
     * Initialize dependencies.
     *
     * @param TaxClassCollectionFactory $taxClassCollectionFactory
     * @param TaxClassSearchResultsBuilder $searchResultsBuilder
     * @param Converter $converter
     * @param ClassModelRegistry $classModelRegistry
     */
    public function __construct(
        TaxClassCollectionFactory $taxClassCollectionFactory,
        TaxClassSearchResultsBuilder $searchResultsBuilder,
        Converter $converter,
        ClassModelRegistry $classModelRegistry
    ) {
        $this->taxClassCollectionFactory = $taxClassCollectionFactory;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->converter = $converter;
        $this->classModelRegistry = $classModelRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function createTaxClass(TaxClassDataObject $taxClass)
    {
        if ($taxClass->getClassId()) {
            throw new InputException(self::CLASS_ID_NOT_ALLOWED);
        }

        $this->validateTaxClassData($taxClass);
        $taxModel = $this->converter->createTaxClassModel($taxClass);
        try {
            $taxModel->save();
        } catch (ModelException $e) {
            if (strpos($e->getMessage(), \Magento\Tax\Model\Resource\TaxClass::UNIQUE_TAX_CLASS_MSG) !== false) {
                throw new InputException(
                    'A class with the same name already exists for ClassType %classType.',
                    ['classType' => $taxClass->getClassType()]
                );
            } else {
                throw $e;
            }
        }
        $this->classModelRegistry->registerTaxClass($taxModel);
        return $taxModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function getTaxClass($taxClassId)
    {
        $taxClassModel = $this->classModelRegistry->retrieve($taxClassId);
        return $this->converter->createTaxClassData($taxClassModel);
    }

    /**
     * {@inheritdoc}
     */
    public function updateTaxClass($taxClassId, TaxClassDataObject $taxClass)
    {
        if ($taxClass->getClassId()) {
            throw new InputException(self::CLASS_ID_NOT_ALLOWED);
        }

        $this->validateTaxClassData($taxClass);

        if (!$taxClassId) {
            throw InputException::invalidFieldValue('taxClassId', $taxClassId);
        }

        $originalTaxClassModel = $this->classModelRegistry->retrieve($taxClassId);

        $taxClassModel = $this->converter->createTaxClassModel($taxClass);
        $taxClassModel->setId($taxClassId);

        /* should not be allowed to switch the tax class type */
        if ($originalTaxClassModel->getClassType() !== $taxClassModel->getClassType()) {
            throw new InputException('Updating classType is not allowed.');
        }

        try {
            $taxClassModel->save();
        } catch (\Exception $e) {
            return false;
        }
        $this->classModelRegistry->registerTaxClass($taxClassModel);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteTaxClass($taxClassId)
    {
        $taxClassModel = $this->classModelRegistry->retrieve($taxClassId);

        try {
            $taxClassModel->delete();
        } catch (LocalizedException $e) {
            throw $e;
        } catch (\Exception $e) {
            return false;
        }
        $this->classModelRegistry->remove($taxClassId);

        return true;
    }

    /**
     * Validate TaxClass Data
     *
     * @param TaxClassDataObject $taxClass
     * @return void
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
        } else if ($classType !== TaxClassServiceInterface::TYPE_CUSTOMER
            && $classType !== TaxClassServiceInterface::TYPE_PRODUCT
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
     * @return \Magento\Tax\Service\V1\Data\TaxClassSearchResults containing Data\TaxClass
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
