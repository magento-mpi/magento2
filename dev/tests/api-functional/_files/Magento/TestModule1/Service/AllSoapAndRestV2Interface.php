<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

interface AllSoapAndRestV2Interface extends \Magento\TestModule1\Service\AllSoapAndRestV1Interface
{

    /**
     * @param $request
     * @return array
     */
    public function delete($request);

}
