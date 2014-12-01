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
     * @return void
     */
    public function addCriteria(\Magento\Cms\Api\PageCriteriaInterface $criteria);

    /**
     * Set first store flag
     *
     * @param bool $flag
     * @return void
     */
    public function setFirstStoreFlag($flag = false);

    /**
     * Add filter by store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @param bool $withAdmin
     * @return void
     */
    public function addStoreFilter($store, $withAdmin = true);
}
