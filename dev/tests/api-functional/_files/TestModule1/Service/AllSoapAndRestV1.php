<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_TestModule1_Service_AllSoapAndRestV1 implements Mage_TestModule1_Service_AllSoapAndRestV1Interface
{
    const ERROR_INTERNAL_LOAD = '01';
    const ERROR_INTERNAL_SAVE = '02';


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
            'name' => 'testProduct1'
        );
        return $result;
    }


    /**
     * @param $request
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
     * @throws Mage_TestModule1_Exception
     */
    public function update($request)
    {
        if ($request['id'] == null) {
            //TODO: Change to Mage_Service_Exception
            throw new Mage_TestModule1_Exception("Invalid Id",
                Mage_TestModule1_Service_AllSoapAndRestV1::ERROR_INTERNAL_SAVE);
        }

        $result = array(
            'id' => $request['id'],
            'name' => 'Updated' . $request['name']
        );
        return $result;
    }
}