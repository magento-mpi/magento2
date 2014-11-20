<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Api;

/**
 * Interface PageCriteriaInterface
 */
interface PageCriteriaInterface extends \Magento\Framework\Api\CriteriaInterface
{
    /**
     * Add Criteria object
     *
     * @param \Magento\Cms\Api\PageCriteriaInterface $criteria
     * @return bool
     */
    public function addCriteria(\Magento\Cms\Api\PageCriteriaInterface $criteria);

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return bool
     */
    public function setFirstStoreFlag($flag = false);

    /**
     * Add filter by store
     *
     * @param int $storeId
     * @param bool $withAdmin
     * @return bool
     */
    public function addStoreFilter($storeId, $withAdmin = true);
}
