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
use Magento\Exception\NoSuchEntityException;

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
     * @var Dto\SearchResultsBuilder
     */
    private $_searchResultsBuilder;

    /**
     * @var Dto\CustomerGroupBuilder
     */
    private $_customerGroupBuilder;

    /**
     * @param GroupFactory $groupFactory
     * @param StoreConfig $storeConfig
     * @param Dto\SearchResultsBuilder $searchResultsBuilder
     * @param Dto\CustomerGroupBuilder $customerGroupBuilder
     */
    public function __construct(
        GroupFactory $groupFactory,
        StoreConfig $storeConfig,
        Dto\SearchResultsBuilder $searchResultsBuilder,
        Dto\CustomerGroupBuilder $customerGroupBuilder
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_storeConfig = $storeConfig;
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
    public function searchGroups(Dto\SearchCriteria $searchCriteria)
    {
        $this->_searchResultsBuilder->setSearchCriteria($searchCriteria);

        $groups = array();
        /** @var Collection $collection */
        $collection = $this->_groupFactory->create()->getCollection();
        foreach ($searchCriteria->getFilters() as $filter) {
            $collection->addFilter($filter->getField(), $filter->getValue(), $filter->getConditionType());
        }
        $this->_searchResultsBuilder->setTotalCount($collection->getSize());
        foreach ($searchCriteria->getSortOrders() as $field => $direction) {
            switch($field) {
                case 'id' :
                    $field = 'customer_group_id';
                    break;
                case 'code':
                    $field = 'customer_group_code';
                    break;
                case "tax_class_id":
                default:
                    break;
            }
            $collection->addOrder($field, $direction == Dto\SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
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
    public function saveGroup(Dto\CustomerGroup $group)
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
    }
}
