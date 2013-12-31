<?php
/**
 * Customer service is responsible for customer business workflow encapsulation
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service;

use Magento\Customer\Service\Entity\V1\CustomerGroup;
use Magento\Customer\Service\Entity\V1\SearchCriteria;
use Magento\Customer\Service\Entity\V1\SearchResults;
use Magento\Reports\Exception;

class CustomerGroupV1 implements CustomerGroupV1Interface
{
    /**
     * @var \Magento\Customer\Model\GroupFactory
     */
    protected $_groupFactory;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    public function __construct(
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\Core\Model\Store\Config $storeConfig
    ) {
        $this->_groupFactory = $groupFactory;
        $this->_storeConfig = $storeConfig;
    }

    /**
     * @inheritdoc
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null)
    {
        $groups = array();
        /** @var \Magento\Customer\Model\Resource\Group\Collection $collection */
        $collection = $this->_groupFactory->create()->getCollection();
        if (!$includeNotLoggedIn) {
            $collection->setRealGroupsFilter();
        }
        if (!is_null($taxClassId)) {
            $collection->addFieldToFilter('tax_class_id', $taxClassId);
        }
        /** @var \Magento\Customer\Model\Group $group */
        foreach ($collection as $group) {
            $groups[] = new Entity\V1\CustomerGroup(
                $group->getId(),
                $group->getCode(),
                $group->getTaxClassId()
            );
        }
        return $groups;
    }

    /**
     * @inheritdoc
     */
    public function searchGroups(SearchCriteria $searchCriteria)
    {
        $searchResults = new SearchResults();
        $searchResults->setSearchCriteria($searchCriteria);

        $groups = array();
        /** @var \Magento\Customer\Model\Resource\Group\Collection $collection */
        $collection = $this->_groupFactory->create()->getCollection();
        foreach ($searchCriteria->getFilters() as $filter) {
            $collection->addFilter($filter->getField(), $filter->getValue(), $filter->getConditionType());
        }
        $searchResults->setTotalCount($collection->getSize());
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
            $collection->addOrder($field, $direction == SearchCriteria::SORT_ASC ? 'ASC' : 'DESC');
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());

        /** @var \Magento\Customer\Model\Group $group */
        foreach ($collection as $group) {
            $groups[] = new Entity\V1\CustomerGroup(
                $group->getId(),
                $group->getCode(),
                $group->getTaxClassId()
            );
        }
        $searchResults->setItems($groups);
        return $searchResults;
    }

    /**
     * @inheritdoc
     */
    public function getGroup($groupId)
    {
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->load($groupId);
        // Throw exception if a customer group does not exist
        if (is_null($customerGroup->getId())) {
            throw new Entity\V1\Exception(__('groupId ' . $groupId . ' does not exist.'));
        }
        return new CustomerGroup($customerGroup->getId(), $customerGroup->getCode(), $customerGroup->getTaxClassId());
    }

    /**
     * @inheritdoc
     */
    public function getDefaultGroup($storeId)
    {
        $groupId = $this->_storeConfig->getConfig(\Magento\Customer\Model\Group::XML_PATH_DEFAULT_ID, $storeId);
        return $this->getGroup($groupId);
    }

    /**
     * @inheritdoc
     */
    public function canDelete($groupId)
    {
        $customerGroup = $this->_groupFactory->create();
        $customerGroup->load($groupId);
        return $groupId > 0 && !$customerGroup->usesAsDefault();
    }

    /**
     * @inheritdoc
     */
    public function saveGroup(Entity\V1\CustomerGroup $group)
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
     * @inheritdoc
     */
    public function deleteGroup($groupId)
    {
        try {
            // Get group so we can throw an exception if it doesn't exist
            $this->getGroup($groupId);
            $customerGroup = $this->_groupFactory->create();
            $customerGroup->setId($groupId);
            $customerGroup->delete();
        } catch (\Exception $e) {
            throw new Entity\V1\Exception($e->getMessage(), $e->getCode(), $e);
        }
    }
}
