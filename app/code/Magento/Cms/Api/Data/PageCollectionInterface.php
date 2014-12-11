<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Page collection interface
 */
namespace Magento\Cms\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Interface PageCollectionInterface
 * @package Magento\Cms\Api\Data
 */
interface PageCollectionInterface extends SearchResultInterface
{
    /**
     * Get items
     *
     * @return \Magento\Cms\Api\Data\PageInterface[]
     */
    public function getItems();

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|page_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray();
}
