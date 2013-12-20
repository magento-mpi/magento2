<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule4\Service;

interface AllSoapAndRestV2Interface
{
    /**
     * @param int $itemId
     * @return Entity\V2\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($itemId);

    /**
     * @return array Entity\V2\AllSoapAndRest[]
     */
    public function items();

    /**
     * @param Entity\V2\AllSoapAndRest $item
     * @return Entity\V2\AllSoapAndRest
     */
    public function create(Entity\V2\AllSoapAndRest $item);

    /**
     * @param Entity\V2\AllSoapAndRest $item
     * @return Entity\V2\AllSoapAndRest
     */
    public function update(Entity\V2\AllSoapAndRest $item);

    /**
     * @param string $itemId
     * @return Entity\V2\AllSoapAndRest
     */
    public function delete($itemId);

}
