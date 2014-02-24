<?php
/**
 * Customer Service Interface
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

use Magento\Exception\InputException;
use Magento\Exception\NoSuchEntityException;

interface CustomerGroupServiceInterface
{
    const NOT_LOGGED_IN_ID          = 0;
    const CUST_GROUP_ALL            = 32000;
    const GROUP_CODE_MAX_LENGTH     = 32;

    /**
     * Retrieve Customer Groups
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param boolean $includeNotLoggedIn
     * @param int $taxClassId
     *
     * @return Data\CustomerGroup[]
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null);

    /**
     * @param Data\SearchCriteria $searchCriteria
     * @throws InputException if there is a problem with the input
     * @return Data\SearchResults containing Data\CustomerGroup objects
     */
    public function searchGroups(Data\SearchCriteria $searchCriteria);

    /**
     * Get a customer group by group ID.
     *
     * @param int $groupId
     * @throws NoSuchEntityException if $groupId is not found
     * @return Data\CustomerGroup
     */
    public function getGroup($groupId);

    /**
     * @param int $storeId
     * @throws NoSuchEntityException if default group for $storeId is not found
     * @return Data\CustomerGroup
     */
    public function getDefaultGroup($storeId);

    /**
     * @param int $groupId
     *
     * @return boolean true, if this group can be deleted
     */
    public function canDelete($groupId);

    /**
     * @param Data\CustomerGroup $group
     * @throws \Exception if something goes wrong during save
     * @return int customer group ID
     */
    public function saveGroup(Data\CustomerGroup $group);

    /**
     * @param int $groupId
     * @throws NoSuchEntityException if $groupId is not found
     * @throws \Exception if something goes wrong during delete
     * @return null
     */
    public function deleteGroup($groupId);
}
