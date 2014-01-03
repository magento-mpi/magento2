<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

interface NoWebApiXmlV1Interface
{
    /**
     * Get an item.
     *
     * @param int $id
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function item($id);

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
     * Retrieve a list of items.
     *
     * @return \Magento\TestModule2\Service\Entity\V1\Item[]
     */
    public function items();
}
