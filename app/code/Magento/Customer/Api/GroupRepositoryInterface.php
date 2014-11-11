<?php
/**
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
     */
    public function save(\Magento\Customer\Api\Data\GroupInterface $group);

    /**
     * Get customer group by group ID.
     *
     * @param int $id
     * @return \Magento\Customer\Api\Data\GroupInterface
     */
    public function get($id);

    /**
     * Retrieve customer groups.
     *
     * The list of groups can be filtered to exclude the NOT_LOGGED_IN group using the first parameter and/or it can
     * be filtered by tax class.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magento\Customer\Api\Data\GroupSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customer group.
     *
     * @param \Magento\Customer\Api\Data\GroupInterface $group
     * @return bool true on success
     */
    public function delete(\Magento\Customer\Api\Data\GroupInterface $group);

    /**
     * Delete customer group by ID.
     *
     * @param int $id
     * @return bool true on success
     */
    public function deleteById($id);
}
