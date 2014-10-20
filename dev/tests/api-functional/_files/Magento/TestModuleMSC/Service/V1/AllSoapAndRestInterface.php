<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModuleMSC\Service\V1;

interface AllSoapAndRestInterface
{
    /**
     * @param int $itemId
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function item($itemId);

    /**
     * @param string $name
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function create($name);

    /**
     * @param \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface $entityItem
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function update(\Magento\TestModuleMSC\Service\V1\Entity\ItemInterface $entityItem);

    /**
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface[]
     */
    public function items();

    /**
     * @param string $name
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function testOptionalParam($name = null);

    /**
     * @param \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface $entityItem
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function itemAnyType(\Magento\TestModuleMSC\Service\V1\Entity\ItemInterface $entityItem);

    /**
     * @return \Magento\TestModuleMSC\Service\V1\Entity\ItemInterface
     */
    public function getPreconfiguredItem();
}
