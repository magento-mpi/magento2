<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service;

interface AllSoapAndRestV1Interface
{
    /**
     * Retrieve an item.
     *
     * @param int $id
     * @return \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($id);

    /**
     * Retrieve all items.
     *
     * @return \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest[]
     */
    public function items();

    /**
     * Create a new item.
     *
     * @param \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest $item
     * @return \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest
     */
    public function create(\Magento\TestModule5\Service\Entity\V1\AllSoapAndRest $item);

    /**
     * Update existing item.
     *
     * @param \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest $item
     * @return \Magento\TestModule5\Service\Entity\V1\AllSoapAndRest
     */
    public function update(\Magento\TestModule5\Service\Entity\V1\AllSoapAndRest $item);
}
