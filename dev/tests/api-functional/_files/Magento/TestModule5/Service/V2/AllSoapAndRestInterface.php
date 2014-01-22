<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule5\Service\V2;

interface AllSoapAndRestInterface
{
    /**
     * Retrieve existing item.
     *
     * @param int $id
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($id);

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
     * @param string $id
     * @return \Magento\TestModule5\Service\Entity\V2\AllSoapAndRest
     */
    public function delete($id);
}
