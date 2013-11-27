<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

use Magento\TestModule1\Service\Entity\V1\Item;

interface AllSoapAndRestV1Interface
{
    /**
     * @param int $id
     * @return \Magento\TestModule1\Service\Entity\V1\Item
     */
    public function item($id);

    /**
     * @param string $name
     * @return \Magento\TestModule1\Service\Entity\V1\Item
     */
    public function create($name);

    /**
     * @param \Magento\TestModule1\Service\Entity\V1\Item $item
     * @return \Magento\TestModule1\Service\Entity\V1\Item
     */
    public function update(Item $item);

    /**
     * @return \Magento\TestModule1\Service\Entity\V1\Item[]
     */
    public function items();
}
