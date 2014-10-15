<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface GroupRepository
{
    /**
     * Save group
     *
     * @param \Magento\Customer\Api\Data\Group $group
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a group ID is sent but the group does not exist
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     *      If saving customer group with customer group code that is used by an existing customer group
     * @throws \Magento\Framework\Model\Exception If something goes wrong during save
     * @return int customer group ID
     */
    public function persist(\Magento\Customer\Api\Data\Group $group);

    /**
     * Get a customer group by group ID.
     *
     * @param int $groupId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $groupId is not found
     * @return \Magento\Customer\Api\Data\Group
     */
    public function get($groupId);

    /**
     * Retrieve Customer Groups
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param \Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria
     *
     * @return \Magento\Customer\Service\V1\Data\CustomerGroupSearchResults containing Data\Group objects
     */
    public function getList(\Magento\Framework\Service\V1\Data\SearchCriteria $searchCriteria);

    /**
     * Delete group
     *
     * @param int $groupId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $groupId is not found
     * @throws \Magento\Framework\Exception\StateException Thrown if cannot delete group
     * @throws \Exception If something goes wrong during delete
     * @return bool True if the group was deleted
     */
    public function remove($groupId);

    /**
     * Retrieve Customer Groups
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param bool $includeNotLoggedIn
     * @param int $taxClassId
     *
     * @return \Magento\Customer\Api\Data\Group[]
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null);
}
