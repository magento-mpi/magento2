<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api;

/**
 * Customer group CRUD interface
 */
interface GroupRepositoryInterface
{
    /**
     * Save customer group.
     *
     * @param \Magento\Customer\Api\Data\GroupInterface $group
     * @return \Magento\Customer\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a group ID is sent but the group does not exist
     * @throws \Magento\Framework\Exception\State\InvalidTransitionException
     *      If saving customer group with customer group code that is used by an existing customer group
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magento\Customer\Api\Data\GroupInterface $group);

    /**
     * Get customer group by group ID.
     *
     * @param int $groupId
     * @return \Magento\Customer\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $groupId is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($groupId);

    /**
     * Retrieve customer groups.
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param \Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\GroupSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\Data\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customer group.
     *
     * @param \Magento\Customer\Api\Data\GroupInterface $group
     * @return bool True if the group was successfully deleted
     * @throws \Magento\Framework\Exception\NoSuchEntityException If the specified customer group does not exist.
     * @throws \Magento\Framework\Exception\StateException Thrown if customer group cannot be deleted
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magento\Customer\Api\Data\GroupInterface $group);
}
