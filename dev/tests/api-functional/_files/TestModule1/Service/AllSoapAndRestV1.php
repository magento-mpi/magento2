<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_TestModule1_Service_AllSoapAndRestV1 implements Magento_TestModule1_Service_AllSoapAndRestV1Interface
{
    /**
     * @param $request
     * @return array
     * @throws Magento_Webapi_Exception
     */
    public function item($request)
    {
        if ($request['id'] == null) {
            throw new Magento_Webapi_Exception("Invalid Id");
        }
        $result = array(
            'id' => $request['id'],
            'name' => 'testProduct1'
        );
        return $result;
    }

    /**
     * @return array
     */
    public function items()
    {
        $result = array(
            array(
                'id' => 1,
                'name' => 'testProduct1'
            ),
            array(
                'id' => 2,
                'name' => 'testProduct2'
            )
        );
        return $result;
    }

    /**
     * @param $request
     * @return array
     */
    public function create($request)
    {
        $result = array(
            'id' => rand(),
            'name' => $request['name']
        );
        return $result;
    }

    /**
     * @param $request
     * @return array
     * @throws Magento_Webapi_Exception
     */
    public function update($request)
    {
        if ($request['id'] == null) {
            throw new Magento_Webapi_Exception("Invalid Id");
        }

        $result = array(
            'id' => $request['id'],
            'name' => 'Updated' . $request['name']
        );
        return $result;
    }
}
