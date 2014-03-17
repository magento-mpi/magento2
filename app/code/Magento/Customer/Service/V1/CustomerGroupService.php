<?php
/**
 * Customer service is responsible for customer business workflow encapsulation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Core\Model\Store\Config as StoreConfig;
use Magento\Core\Model\StoreManagerInterface;
use Magento\Customer\Model\Group as CustomerGroupModel;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\Resource\Group\Collection;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Exception\StateException;
use Magento\Service\V1\Data\Filter;

/**
 * Class CustomerGroupService
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerGroupService implements CustomerGroupServiceInterface
{
    /**
     * @var GroupFactory
     */
    private $_groupFactory;

    /**
     * @var StoreConfig
     */
    private $_storeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var Data\SearchResultsBuilder
     */
    private $_searchResultsBuilder;

    /**
     * @var Data\CustomerGroupBuilder
     */
    private $_customerGroupBuilder;

    /**
     * @param GroupFactory $groupFactory
     * @param StoreConfig $storeConfig
     * @param Data\SearchResultsBuilder $searchResultsBuilder
     * @param Data\CustomerGroupBuilder $customerGroupBuilder
     */
    public function __construct(
        GroupFactory $groupFactory,
        StoreConfig $storeConfig,
        StoreManagerInterface $storeManager,
        Data\SearchResultsBuilder $searchResultsBuilder,
        Data\CustomerGroupBuilder $customerGroupBuilder
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_storeConfig = $storeConfig;
        $this->_storeManager = $storeManager;
        $this->_searchResultsBuilder = $searchResultsBuilder;
        $this->_customerGroupBuilder = $customerGroupBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null)
    {
        $groups = array();
        /** @var Collection $collection */
        $collection = $this->_groupFactory->create()->getCollection();
        if (!$includeNotLoggedIn) {
            $collection->setRealGroupsFilter();
        }
        if (!is_null($taxClassId)) {
            $collection->addFieldToFilter('tax_class_id', $taxClassId);
        }
        /** @var CustomerGroupModel $group */
        foreach ($collection as $group) {
            $this->_customerGroupBuilder->setId($group->getId())
                ->setCode($group->getCode())
                ->setTaxClassId($group->getTaxClassId());
            $groups[] = $this->_customerGroupBuilder->create();
        }
        return $groups;
    }

    /**
     * {@inheritdoc}
     */
    public function searchGroups(Data\SearchCriteria $searchCriteria)
    {
        $this->_searchResultsBuilder->setSearchCriteria($searchCriteria);

        $groups = array();
        /** @var Collection $collection */
        $collection = $this->_groupFactory->create()->getCollection();
        $this->addFiltersFromRootToCollection($searchCriteria->getAndGroup(), $collection);
        $this->_searchResultsBuilder->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $field => $direction) {
                $field = $this->translateField($field);
                $collection->addOrder($field, $direction == Data\SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var CustomerGroupModel $group */
        foreach ($collection as $group) {
            $this->_customerGroupBuilder->setId($group->getId())
                ->setCode($group->getCode())
                ->setTaxClassId($group->getTaxClassId());
            $groups[] = $this->_customerGroupBuilder->create();
        }
        $this->_searchResultsBuilder->setItems($groups);
        return $this->_searchResultsBuilder->create();
    }

    /**
     * Adds some filters from a root filter group to a collection.
     *
     * @param Data\Search\AndGroup $rootAndGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addFiltersFromRootToCollection(Data\Search\AndGroup $rootAndGroup, Collection $collection)
    {
        if (count($rootAndGroup->getAndGroups())) {
            throw new InputException('Only OR groups are supported as nested groups.');
        }

        foreach ($rootAndGroup->getFilters() as $filter) {
            $this->addFilterToCollection($collection, $filter);
        }

        foreach ($rootAndGroup->getOrGroups() as $group) {
            $this->addOrFilterGroupToCollection($collection, $group);
        }
    }

    /**
     * Helper function that adds a filter to the collection
     *
     * @param Collection $collection
     * @param Filter $filter
     * @return void
     */
    protected function addFilterToCollection(Collection $collection, Filter $filter)
    {
        $field = $this->translateField($filter->getField());
        $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
        $collection->addFieldToFilter($field, [$condition => $filter->getValue()]);
    }

    /**
     * Helper function that adds a OrGroup to the collection.
     *
     * @param Collection $collection
     * @param Data\Search\OrGroup $orGroup
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addOrFilterGroupToCollection(Collection $collection, Data\Search\OrGroup $orGroup)
    {
        $fields = [];
        $conditions = [];
        foreach ($orGroup->getFilters() as $filter) {
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
            case 'code':
                return 'customer_group_code';
            case 'id':
                return 'customer_group_id';
            default:
                return $field;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getGroup($groupId)
    {
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->load($groupId);
        // Throw exception if a customer group does not exist
        if (is_null($customerGroup->getId())) {
            throw new NoSuchEntityException('groupId', $groupId);
        }
        $this->_customerGroupBuilder->setId($customerGroup->getId())
            ->setCode($customerGroup->getCode())
            ->setTaxClassId($customerGroup->getTaxClassId());
        return $this->_customerGroupBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultGroup($storeId)
    {
        try {
            $this->_storeManager->getStore($storeId);
        } catch (\Exception $e) {
            throw new NoSuchEntityException('storeId', $storeId);
        }

        $groupId = $this->_storeConfig->getConfig(CustomerGroupModel::XML_PATH_DEFAULT_ID, $storeId);
        try {
            return $this->getGroup($groupId);
        } catch (NoSuchEntityException $e) {
            $e->addField('storeId', $storeId);
            throw $e;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function canDelete($groupId)
    {
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->load($groupId);
        return $groupId > 0 && !$customerGroup->usesAsDefault();
    }

    /**
     * {@inheritdoc}
     */
    public function saveGroup(Data\CustomerGroup $group)
    {
        $customerGroup = $this->_groupFactory->create();
        if ($group->getId()) {
            $customerGroup->load($group->getId());
        }
        $customerGroup->setCode($group->getCode());
        $customerGroup->setTaxClassId($group->getTaxClassId());
        $customerGroup->save();
        return $customerGroup->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteGroup($groupId)
    {
        if (!$this->canDelete($groupId)) {
            throw new StateException(__("Cannot delete group."));
        }

        // Get group so we can throw an exception if it doesn't exist
        $this->getGroup($groupId);
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->setId($groupId);
        $customerGroup->delete();
        return true;
    }
}
