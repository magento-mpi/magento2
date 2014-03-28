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
use Magento\Customer\Model\Group as CustomerGroupModel;
use Magento\Customer\Model\GroupFactory;
use Magento\Customer\Model\Resource\Group\Collection;
use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;
use Magento\Core\Model\StoreManagerInterface;

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
     * @var Data\SearchResultsBuilder
     */
    private $_searchResultsBuilder;

    /**
     * @var Data\CustomerGroupBuilder
     */
    private $_customerGroupBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @param GroupFactory $groupFactory
     * @param StoreConfig $storeConfig
     * @param Data\SearchResultsBuilder $searchResultsBuilder
     * @param Data\CustomerGroupBuilder $customerGroupBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GroupFactory $groupFactory,
        StoreConfig $storeConfig,
        Data\SearchResultsBuilder $searchResultsBuilder,
        Data\CustomerGroupBuilder $customerGroupBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_storeConfig = $storeConfig;
        $this->_searchResultsBuilder = $searchResultsBuilder;
        $this->_customerGroupBuilder = $customerGroupBuilder;
        $this->_storeManager = $storeManager;
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
            $this->_customerGroupBuilder->setId(
                $group->getId()
            )->setCode(
                    $group->getCode()
                )->setTaxClassId(
                    $group->getTaxClassId()
                );
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
        $this->addFiltersToCollection($searchCriteria->getFilters(), $collection);
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
            $this->_customerGroupBuilder->setId(
                $group->getId()
            )->setCode(
                    $group->getCode()
                )->setTaxClassId(
                    $group->getTaxClassId()
                );
            $groups[] = $this->_customerGroupBuilder->create();
        }
        $this->_searchResultsBuilder->setItems($groups);
        return $this->_searchResultsBuilder->create();
    }

    /**
     * Adds some filters from a filter group to a collection.
     *
     * @param Data\Search\FilterGroupInterface $filterGroup
     * @param Collection $collection
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addFiltersToCollection(Data\Search\FilterGroupInterface $filterGroup, Collection $collection)
    {
        if (strcasecmp($filterGroup->getGroupType(), 'AND')) {
            throw new InputException('Only AND grouping is currently supported for filters.');
        }

        foreach ($filterGroup->getFilters() as $filter) {
            $this->addFilterToCollection($collection, $filter);
        }

        foreach ($filterGroup->getGroups() as $group) {
            $this->addFilterGroupToCollection($collection, $group);
        }
    }

    /**
     * Helper function that adds a filter to the collection
     *
     * @param Collection $collection
     * @param Data\Filter $filter
     * @return void
     */
    protected function addFilterToCollection(Collection $collection, Data\Filter $filter)
    {
        $field = $this->translateField($filter->getField());
        $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
        $collection->addFieldToFilter($field, array($condition => $filter->getValue()));
    }

    /**
     * Helper function that adds a FilterGroup to the collection.
     *
     * @param Collection $collection
     * @param Data\Search\FilterGroupInterface $group
     * @return void
     * @throws \Magento\Exception\InputException
     */
    protected function addFilterGroupToCollection(Collection $collection, Data\Search\FilterGroupInterface $group)
    {
        if (strcasecmp($group->getGroupType(), 'OR')) {
            throw new InputException('The only nested groups currently supported for filters are of type OR.');
        }
        $fields = array();
        $conditions = array();
        foreach ($group->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $this->translateField($filter->getField());
            $conditions[] = array($condition => $filter->getValue());
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
        $this->_customerGroupBuilder->setId(
            $customerGroup->getId()
        )->setCode(
                $customerGroup->getCode()
            )->setTaxClassId(
                $customerGroup->getTaxClassId()
            );
        return $this->_customerGroupBuilder->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultGroup($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->_storeManager->getStore()->getId();
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
        // Get group so we can throw an exception if it doesn't exist
        $this->getGroup($groupId);
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->setId($groupId);
        $customerGroup->delete();
        return true;
    }
}
