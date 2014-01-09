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

use Magento\Customer\Service\V1\Dto\CustomerGroup;
use Magento\Customer\Service\V1\Dto\SearchCriteria;
use Magento\Validator\Test\True;

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
     *
     * @return \Magento\Customer\Service\V1\Dto\SearchResults
     */
    public function searchGroups(Dto\SearchCriteria $searchCriteria);

    /**
     * Get a customer group by group ID.
     *
     * @param int $groupId
     * @throws \Magento\Customer\Service\Entity\V1\Exception if groupId is not found
     * @return \Magento\Customer\Service\V1\Dto\CustomerGroup
     */
    public function getGroup($groupId);

    /**
     * @param int $storeId
     *
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
     *
     * @return int customer group ID
     */
    public function saveGroup(CustomerGroup $group);

    /**
     * @param int $groupId
     *
     * @return null
     */
    public function deleteGroup($groupId);
}
