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
     * @return Item
     */
    public function item($id);

    /**
     * @param string $name
     * @return Item
     */
    public function create($name);

    /**
     * @param Item $item
     * @return Item
     */
    public function update(Item $item);

    /**
     * @return array
     */
    public function items();
}
