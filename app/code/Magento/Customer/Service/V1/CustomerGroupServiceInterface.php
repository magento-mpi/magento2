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
    const XML_PATH_DEFAULT_ID = 'customer/create_account/default_group';

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
     * @param \Magento\Framework\Data\SearchCriteria $searchCriteria
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @return \Magento\Customer\Service\V1\Data\CustomerGroupSearchResults containing Data\CustomerGroup objects
     */
    public function searchGroups(\Magento\Framework\Data\SearchCriteria $searchCriteria);

    /**
     * Get a customer group by group ID.
     *
     * @param string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup
     */
    public function getGroup($id);

    /**
     * Get default group
     *
     * @param string $storeId Defaults the current store
     * @throws \Magento\Framework\Exception\NoSuchEntityException If default group for $storeId is not found
     * @return \Magento\Customer\Service\V1\Data\CustomerGroup
     */
    public function getDefaultGroup($storeId = null);

    /**
     * Check if the group can be deleted
     *
     * @param string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     * @return bool True, if this group can be deleted
     */
    public function canDelete($id);

    /**
     * Create group
     *
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup $group
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     *      If saving customer group with customer group code that is used by an existing customer group
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return int customer group ID
     */
    public function createGroup(\Magento\Customer\Service\V1\Data\CustomerGroup $group);

    /**
     * Update group
     *
     * @param string $id
     * @param \Magento\Customer\Service\V1\Data\CustomerGroup $group
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a group ID is sent but the group does not exist
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     *      If saving customer group with customer group code that is used by an existing customer group
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return bool True if this group was updated
     */
    public function updateGroup($id, \Magento\Customer\Service\V1\Data\CustomerGroup $group);

    /**
     * Delete group
     *
     * @param string $id
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @throws \Magento\Framework\Exception\StateException Thrown if cannot delete group
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the group was deleted
     */
    public function deleteGroup($id);
}
