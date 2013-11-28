<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service;

interface AllSoapAndRestV2Interface
{
    /**
     * Retrieve existing item.
     *
     * @param int $itemId
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($itemId);

    /**
     * Retrieve a list of all existing items.
     *
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest[]
     */
    public function items();

    /**
     * Add new item.
     *
     * @param \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest $item
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     */
    public function create(\Magento\TestModule5\Service\Entity\V2\AllSoapAndRest $item);

    /**
     * Update one item.
     *
     * @param \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest $item
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     */
    public function update(\Magento\TestModule5\Service\Entity\V2\AllSoapAndRest $item);

    /**
     * Delete existing item.
     *
     * @param string $itemId
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     */
    public function delete($itemId);

}
