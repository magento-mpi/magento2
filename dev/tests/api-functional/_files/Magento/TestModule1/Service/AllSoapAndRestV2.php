<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

class AllSoapAndRestV2 extends \Magento\TestModule1\Service\AllSoapAndRestV1 implements
    \Magento\TestModule1\Service\AllSoapAndRestV2Interface
{
    /**
     * @param $request
     * @return array
     * @throws \Magento\Webapi\Exception
     */
    public function item($request)
    {
        if ($request['id'] == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }
        $result = array(
            'id' => $request['id'],
            'name' => 'testProduct1',
            'price' => '1'
        );
        return $result;
    }


    /**
     * @param $request
     * @return array
     * @throws \Magento\Webapi\Exception
     */
    public function delete($request)
    {
        if ($request['id'] == null) {
            throw new \Magento\Webapi\Exception("Invalid Id");
        }

        $result = array(
            'id' => $request['id'],
            'name' => 'testProduct1'
        );
        return $result;
    }
}
