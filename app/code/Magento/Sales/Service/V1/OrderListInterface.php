<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Service\V1;

use Magento\Framework\Service\V1\Data\SearchCriteria;

/**
 * Interface OrderListInterface
 */
interface OrderListInterface
{
    /**
     * Invoke OrderList service
     *
     * @param SearchCriteria $criteria
     * @return \Magento\Catalog\Service\V1\Data\Product\SearchResults
     */
    public function invoke(SearchCriteria $criteria);
}
