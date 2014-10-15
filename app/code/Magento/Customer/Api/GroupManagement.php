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
 * Interface for managing customer groups.
 */
interface GroupManagement
{
    /**
     * Check if customer group can be deleted.
     *
     * @param int $groupId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     */
    public function isReadonly($groupId);

    /**
     * Get default customer group.
     *
     * @param int $storeId
     * @return \Magento\Customer\Api\Data\Group
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDefaultGroup($storeId = null);
}
