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
     * @param int $itemId
     * @return Entity\V1\AllSoapAndRest
     * @throws \Magento\Webapi\Exception
     */
    public function item($itemId);

    /**
     * @return array Entity\V1\AllSoapAndRest[]
     */
    public function items();

    /**
     * @param Entity\V1\AllSoapAndRest $item
     * @return Entity\V1\AllSoapAndRest
     */
    public function create(Entity\V1\AllSoapAndRest $item);

    /**
     * @param Entity\V1\AllSoapAndRest $item
     * @return Entity\V1\AllSoapAndRest
     */
    public function update(Entity\V1\AllSoapAndRest $item);

}
