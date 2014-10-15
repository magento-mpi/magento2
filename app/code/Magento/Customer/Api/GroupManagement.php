<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Api;

interface GroupManagement
{
    /**
     * Check if the group can be deleted
     *
     * @param int $groupId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     * @return bool
     */
    public function getIsReadonly($groupId);

    /**
     * Get default group
     *
     * @param int $storeId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If default group for $storeId is not found
     * @return \Magento\Customer\Api\Data\Group
     */
    public function getDefaultGroup($storeId = null);
}
