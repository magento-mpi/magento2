<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1;

use Magento\TestModuleMSC\Service\V1\Entity\Item;

interface AllSoapAndRestInterface
{
    /**
     * @param int $itemId
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function item($itemId);

    /**
     * @param string $name
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function create($name);

    /**
     * @param \Magento\TestModuleMSC\Service\V1\Entity\Item $entityItem
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function update(Item $entityItem);

    /**
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item[]
     */
    public function items();

    /**
     * @param string $name
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function testOptionalParam($name = null);

    /**
     * @param \Magento\TestModuleMSC\Service\V1\Entity\Item $entityItem
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function itemAnyType(Item $entityItem);

    /**
     * @return \Magento\TestModuleMSC\Service\V1\Entity\Item
     */
    public function getPreconfiguredItem();
}
