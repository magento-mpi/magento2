<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block collection interface
 */
namespace Magento\Cms\Api\Data;

use Magento\Framework\Data\SearchResultInterface;

/**
 * Interface BlockCollectionInterface
 * @package Magento\Cms\Api\Data
 */
interface BlockCollectionInterface extends SearchResultInterface
{
    /**
     * Get items
     *
     * @return \Magento\Cms\Api\Data\BlockInterface[]
     */
    public function getItems();

    /**
     * Returns pairs identifier - title for unique identifiers
     * and pairs identifier|block_id - title for non-unique after first
     *
     * @return array
     */
    public function toOptionIdArray();
}
