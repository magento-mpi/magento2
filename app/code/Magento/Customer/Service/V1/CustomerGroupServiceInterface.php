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
     * @return \Magento\Customer\Service\V1\Dto\CustomerGroup[]
     */
    public function getGroups($includeNotLoggedIn = true, $taxClassId = null);

    /**
     * @param \Magento\Customer\Service\V1\Dto\SearchCriteria $searchCriteria
     * @throws InputException If there is a problem with the input
     * @return \Magento\Customer\Service\V1\Dto\SearchResults containing Dto\CustomerGroup objects
     */
    public function searchGroups(\Magento\Customer\Service\V1\Dto\SearchCriteria $searchCriteria);

    /**
     * Get a customer group by group ID.
     *
     * @param int $groupId
     * @throws NoSuchEntityException If $groupId is not found
     * @return \Magento\Customer\Service\V1\Dto\CustomerGroup
     */
    public function getGroup($groupId);

    /**
     * @param int $storeId
     * @throws NoSuchEntityException If default group for $storeId is not found
     * @return \Magento\Customer\Service\V1\Dto\CustomerGroup
     */
    public function getDefaultGroup($storeId);

    /**
     * @param int $groupId
     *
     * @return boolean true, if this group can be deleted
     */
    public function canDelete($groupId);

    /**
     * @param \Magento\Customer\Service\V1\Dto\CustomerGroup $group
     * @throws \Exception If something goes wrong during save
     * @return int customer group ID
     */
    public function saveGroup(\Magento\Customer\Service\V1\Dto\CustomerGroup $group);

    /**
     * @param int $groupId
     * @throws NoSuchEntityException If $groupId is not found
     * @throws \Exception If something goes wrong during delete
     * @return bool Should return true if the group was deleted
     */
    public function deleteGroup($groupId);
}
