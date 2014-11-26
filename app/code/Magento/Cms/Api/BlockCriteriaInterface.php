<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Api;

/**
 * Interface BlockCriteriaInterface
 */
interface BlockCriteriaInterface extends \Magento\Framework\Api\CriteriaInterface
{
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
     * @param int $storeId
     * @param bool $withAdmin
     * @return void
     */
    public function addStoreFilter($storeId, $withAdmin = true);
}
