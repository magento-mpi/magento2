<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

interface SubsetRestV1Interface
{
    /**
     * Return a single item.
     *
     * @param int $id
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function item($id);

    /**
     * Return multiple items.
     *
     * @return \Magento\TestModule2\Service\Entity\V1\Item[]
     */
    public function items();

    /**
     * Create an item.
     *
     * @param string $name
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function create($name);

    /**
     * Update an item.
     *
     * @param \Magento\TestModule2\Service\Entity\V1\Item $item
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function update(Item $item);

    /**
     * Delete an item.
     *
     * @param int $id
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function remove($id);
}
