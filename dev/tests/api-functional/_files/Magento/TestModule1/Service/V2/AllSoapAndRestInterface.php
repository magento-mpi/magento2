<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service\V2;

use Magento\TestModule1\Service\V2\Entity\Item;

interface AllSoapAndRestInterface
{
    /**
     * Get item.
     *
     * @param int $id
     * @return \Magento\TestModule1\Service\V2\Entity\Item
     */
    public function item($id);

    /**
     * Create item.
     *
     * @param string $name
     * @return \Magento\TestModule1\Service\V2\Entity\Item
     */
    public function create($name);

    /**
     * Update item.
     *
     * @param \Magento\TestModule1\Service\V2\Entity\Item $item
     * @return \Magento\TestModule1\Service\V2\Entity\Item
     */
    public function update(Item $item);

    /**
     * Retrieve a list of items.
     *
     * @param string[] $filters
     * @param string $sortOrder
     * @return \Magento\TestModule1\Service\V2\Entity\Item[]
     */
    public function items($filters = array(), $sortOrder = 'ASC');

    /**
     * Delete an item.
     *
     * @param int $id
     * @return \Magento\TestModule1\Service\V2\Entity\Item
     */
    public function delete($id);
}
