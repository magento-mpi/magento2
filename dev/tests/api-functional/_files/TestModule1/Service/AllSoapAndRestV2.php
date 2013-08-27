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

    const ERROR_INTERNAL_DELETE = '03';


    /**
     * @param $request
     * @return array
     * @throws Mage_TestModule1_Exception
     */
    public function item($request)
    {
        if ($request['id'] == null) {
            //TODO: Change to Mage_Service_Exception
            throw new Mage_TestModule1_Exception("Invalid Id",
                Mage_TestModule1_Service_AllSoapAndRestV1::ERROR_INTERNAL_LOAD);
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
     * @throws Mage_TestModule1_Exception
     */
    public function delete($request)
    {
        if ($request['id'] == null) {
            //TODO: Change to Mage_Service_Exception
            throw new Mage_TestModule1_Exception("Invalid Id",
                Mage_TestModule1_Service_AllSoapAndRestV2::ERROR_INTERNAL_DELETE);
        }

        $result = array(
            'id' => $request['id'],
            'name' => 'testProduct1'
        );
        return $result;
    }

}