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
interface GroupManagementInterface
{
    const XML_PATH_DEFAULT_ID = 'customer/create_account/default_group';

    /**
     * Check if customer group can be deleted.
     *
     * @param int $groupId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException If group is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isReadonly($groupId);

    /**
     * Get default customer group.
     *
     * @param int $storeId
     * @return \Magento\Customer\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getDefaultGroup($storeId = null);
}
