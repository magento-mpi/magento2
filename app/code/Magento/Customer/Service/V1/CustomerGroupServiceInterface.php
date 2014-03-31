<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Interface CustomerGroupServiceInterface
 */
interface CustomerGroupServiceInterface
{
    const NOT_LOGGED_IN_ID = 0;

    const CUST_GROUP_ALL = 32000;

    const GROUP_CODE_MAX_LENGTH = 32;

    /**
     * Retrieve Customer Groups
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param bool $includeNotLoggedIn
     * @param int $taxClassId
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup[]
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null);

    /**
     * Search groups
     *
     * @param \Magento\Customer\Service\V1\Data\SearchCriteria $searchCriteria
     * @throws \Magento\Exception\InputException If there is a problem with the input
     * @return \Magento\Customer\Service\V1\Data\SearchResults containing Data\CustomerGroup objects
     */
    public function searchGroups(\Magento\Customer\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Get a customer group by group ID.
     *
     * @param int $groupId
     * @throws \Magento\Exception\NoSuchEntityException If $groupId is not found
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup
     */
    public function getGroup($groupId);

    /**
     * Get default group
     *
     * @param int|null $storeId Defaults the current store
     * @throws \Magento\Exception\NoSuchEntityException If default group for $storeId is not found
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup
     */
    public function getDefaultGroup($storeId = null);

    /**
     * Check if the group can be deleted
     *
     * @param int $groupId
     * @return bool True, if this group can be deleted
     */
    public function canDelete($groupId);

    /**
     * Save group
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup $group
     * @throws \Exception If something goes wrong during save
     * @return int customer group ID
     */
    public function saveGroup(\Magento\Customer\Service\V1\Data\CustomerGroup $group);

    /**
     * Delete group
     *
     * @param int $groupId
     * @throws \Magento\Exception\NoSuchEntityException If $groupId is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the group was deleted
     */
    public function deleteGroup($groupId);
}
