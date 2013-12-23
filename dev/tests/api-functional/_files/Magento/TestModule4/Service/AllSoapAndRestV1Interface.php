<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service;

interface AllSoapAndRestV1Interface
{

    /**
     * Retrieve an item.
     *
     * @param int $itemId
     * @return \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($itemId);

    /**
     * Retrieve all items.
     *
     * @return \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest[]
     */
    public function items();

    /**
     * Create a new item.
     *
     * @param \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest $item
     * @return \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest
     */
    public function create(\Magento\TestModule4\Service\Entity\V1\AllSoapAndRest $item);

    /**
     * Update existing item.
     *
     * @param \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest $item
     * @return \Magento\TestModule4\Service\Entity\V1\AllSoapAndRest
     */
    public function update(\Magento\TestModule4\Service\Entity\V1\AllSoapAndRest $item);

}
