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
     * @param int $id
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function item($id);

    /**
     * @param string $name
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function create($name);

    /**
     * @param \Magento\TestModule2\Service\Entity\V1\Item $item
     * @return \Magento\TestModule2\Service\Entity\V1\Item
     */
    public function update(Item $item);

    /**
     * @return \Magento\TestModule2\Service\Entity\V1\Item[]
     */
    public function items();
}
