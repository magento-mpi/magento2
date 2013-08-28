<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_TestModule1_Service_AllSoapAndRestV2 extends Mage_TestModule1_Service_AllSoapAndRestV1 implements
    Mage_TestModule1_Service_AllSoapAndRestV2Interface
{
    /**
     * @param $request
     * @return array
     * @throws Mage_Webapi_Exception
     */
    public function item($request)
    {
        if ($request['id'] == null) {
            throw new Mage_Webapi_Exception("Invalid Id", Mage_Webapi_Exception::HTTP_BAD_REQUEST);
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
     * @throws Mage_Webapi_Exception
     */
    public function delete($request)
    {
        if ($request['id'] == null) {
            throw new Mage_Webapi_Exception("Invalid Id", Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }

        $result = array(
            'id' => $request['id'],
            'name' => 'testProduct1'
        );
        return $result;
    }
}
