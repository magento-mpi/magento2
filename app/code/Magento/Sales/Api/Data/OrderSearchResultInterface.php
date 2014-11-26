<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Api\Data;

/**
 * Interface OrderSearchResultInterface
 */
interface OrderSearchResultInterface
{
    /**
     * Get collection items
     *
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface[]
     */
    public function getItems();
}
