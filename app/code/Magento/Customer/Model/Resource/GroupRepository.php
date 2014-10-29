<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Resource;

use Magento\Customer\Model\Resource\Group\Collection;
use Magento\Customer\Service\V1\Data\CustomerGroupSearchResultsBuilder;
use Magento\Framework\Api\Data\SearchCriteriaInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Framework\Service\V1\Data\Search\FilterGroup;
use Magento\Tax\Service\V1\Data\TaxClass;
use Magento\Tax\Service\V1\TaxClassServiceInterface;

/**
 * Customer group CRUD class
 */
class GroupRepository implements \Magento\Customer\Api\GroupRepositoryInterface {

    /**
     * The default tax class id if no tax class id is specified
     */
    const DEFAULT_TAX_CLASS_ID = 3;

    /**
     * @var \Magento\Customer\Model\GroupRegistry
     */
    protected $groupRegistry;

    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $groupFactory;

    /**
     * @var \Magento\Customer\Api\Data\GroupDataBuilder
     */
    protected $groupBuilder;

    /**
     * @var \Magento\Customer\Model\Resource\Group
     */
    protected $groupResourceModel;

    /**
     * @var \Magento\Webapi\Model\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var  CustomerGroupSearchResultsBuilder
     */
    protected $searchResultsBuilder;

    /**
     * @var TaxClassServiceInterface
     */
    private $taxClassService;

    /**
     * @param \Magento\Customer\Model\GroupRegistry $groupRegistry
     * @param \Magento\Customer\Model\GroupFactory $groupFactory
     * @param \Magento\Customer\Api\Data\GroupDataBuilder $groupBuilder
     * @param \Magento\Customer\Model\Resource\Group $groupResourceModel
     * @param \Magento\Webapi\Model\DataObjectProcessor $dataObjectProcessor
     * @param CustomerGroupSearchResultsBuilder $searchResultsBuilder
     * @param TaxClassServiceInterface $taxClassService
     */
    public function __construct(
        \Magento\Customer\Model\GroupRegistry $groupRegistry,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Customer\Api\Data\GroupDataBuilder $groupBuilder,
        \Magento\Customer\Model\Resource\Group $groupResourceModel,
        \Magento\Webapi\Model\DataObjectProcessor $dataObjectProcessor,
        CustomerGroupSearchResultsBuilder $searchResultsBuilder,
        TaxClassServiceInterface $taxClassServiceInterface
    ) {
        $this->groupRegistry = $groupRegistry;
        $this->groupFactory = $groupFactory;
        $this->groupBuilder = $groupBuilder;
        $this->groupResourceModel = $groupResourceModel;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->searchResultsBuilder = $searchResultsBuilder;
        $this->taxClassService = $taxClassServiceInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Customer\Api\Data\GroupInterface $group)
    {
        $this->_validate($group);

        /** @var \Magento\Customer\Model\Group $groupModel */
        $groupModel = null;
        if ($group->getId()) {
            $groupModel = $this->groupRegistry->retrieve($group->getId());
            $groupDataAttributes = $this->dataObjectProcessor->buildOutputDataArray(
                $group,
                '\Magento\Customer\Api\Data\GroupInterface'
            );
            foreach ($groupDataAttributes as $attributeCode => $attributeData) {
                $groupModel->setDataUsingMethod($attributeCode, $attributeData);
            }
        } else {
            $groupModel = $this->groupFactory->create();
            $groupModel->setCode($group->getCode());

            $taxClassId = $group->getTaxClassId() ? : self::DEFAULT_TAX_CLASS_ID;
            $this->_verifyTaxClassModel($taxClassId, $group);
            $groupModel->setTaxClassId($taxClassId);
        }

        try {
            $this->groupResourceModel->save($groupModel);
        } catch (\Magento\Framework\Model\Exception $e) {
            /**
             * Would like a better way to determine this error condition but
             *  difficult to do without imposing more database calls
             */
            if ($e->getMessage() === __('Customer Group already exists.')) {
                throw new InvalidTransitionException('Customer Group already exists.');
            }
            throw $e;
        }

        $this->groupRegistry->remove($groupModel->getId());
        return $groupModel->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function get($groupId)
    {
        $groupModel = $this->groupRegistry->retrieve($groupId);
        return $this->groupBuilder
            ->populateWithArray($groupModel->getData())
            ->setId($groupModel->getId())
            ->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $this->searchResultsBuilder->setSearchCriteria($searchCriteria);

        /** @var \Magento\Customer\Model\Resource\Group\Collection $collection */
        $collection = $this->groupFactory->create()->getCollection()->addTaxClass();

        //Add filters from root filter group to the collection
        /** @var FilterGroup $group */
        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $this->searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        /** @var \Magento\Framework\Service\V1\Data\SortOrder $sortOrder */
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $field = $this->translateField($sortOrder->getField());
                $collection->addOrder(
                    $field,
                    ($sortOrder->getDirection() == SearchCriteriaInterface::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \Magento\Customer\Api\Data\GroupInterface[] $groups */
        $groups = array();
        /** @var \Magento\Customer\Model\Group $group */
        foreach ($collection as $group) {
            $groups[] = $group->getDataModel();
        }
        return $this->searchResultsBuilder->setItems($groups)->create();
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
            case Group::CODE:
                return 'customer_group_code';
            case Group::ID:
                return 'customer_group_id';
            default:
                return $field;
        }
    }

    /**
     * Delete customer group.
     *
     * @param \Magento\Customer\Api\Data\GroupInterface $group
     * @return bool true on success
     * @throws \Magento\Framework\Exception\StateException If customer group cannot be deleted
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\Customer\Api\Data\GroupInterface $group)
    {
        return $this->deleteById($group->getId());
    }

    /**
     * Delete customer group by ID.
     *
     * @param int $groupId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException If customer group cannot be deleted
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($groupId)
    {
        $groupModel = $this->groupRegistry->retrieve($groupId);

        if ($groupId <= 0 || $groupModel->usesAsDefault()) {
            throw new \Magento\Framework\Exception\StateException('Cannot delete group.');
        }

        $groupModel->delete();
        $this->groupRegistry->remove($groupId);
        return true;
    }

    /**
     * Validate group values.
     *
     * @param \Magento\Customer\Api\Data\GroupInterface $group
     * @throws InputException
     * @return void
     *
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function _validate($group)
    {
        $exception = new InputException();
        if (!\Zend_Validate::is($group->getCode(), 'NotEmpty')) {
            $exception->addError(InputException::REQUIRED_FIELD, ['fieldName' => 'code']);
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * Verifies that the tax class model exists and is a customer tax class type.
     *
     * @param int $taxClassId The id of the tax class model to check
     * @param \Magento\Customer\Api\Data\GroupInterface $group The original group parameters
     * @return void
     * @throws InputException Thrown if the tax class model is invalid
     */
    protected function _verifyTaxClassModel($taxClassId, $group)
    {
        try {
            /* @var TaxClass $taxClassData */
            $taxClassData = $this->taxClassService->getTaxClass($taxClassId);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            throw InputException::invalidFieldValue('taxClassId', $group->getTaxClassId());
        }
        if ($taxClassData->getClassType() !== TaxClassServiceInterface::TYPE_CUSTOMER) {
            throw InputException::invalidFieldValue('taxClassId', $group->getTaxClassId());
        }
    }
}