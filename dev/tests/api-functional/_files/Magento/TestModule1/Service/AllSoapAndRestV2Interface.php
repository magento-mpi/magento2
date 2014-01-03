<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

use Magento\TestModule1\Service\Entity\V2\Item;

interface AllSoapAndRestV2Interface
{
    /**
     * Get item.
     *
     * @param int $id
     * @return \Magento\TestModule1\Service\Entity\V2\Item
     */
    public function item($id);

    /**
     * Create item.
     *
     * @param string $name
     * @return \Magento\TestModule1\Service\Entity\V2\Item
     */
    public function create($name);

    /**
     * Update item.
     *
     * @param \Magento\TestModule1\Service\Entity\V2\Item $item
     * @return \Magento\TestModule1\Service\Entity\V2\Item
     */
    public function update(Item $item);

    /**
     * Retrieve a list of items.
     *
     * @return \Magento\TestModule1\Service\Entity\V2\Item[]
     */
    public function items();

    /**
     * Delete an item.
     *
     * @param int $id
     * @return \Magento\TestModule1\Service\Entity\V2\Item
     */
    public function delete($id);
}
