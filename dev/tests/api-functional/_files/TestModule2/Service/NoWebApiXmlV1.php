<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_NoWebApiXmlV1 implements Mage_TestModule2_Service_NoWebApiXmlInterfaceV1
{
    /**
     * @param array $request
     * @return array
     */
    public function item($request)
    {
        return array(
            'id' => $request['id']
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function items($request)
    {
        return array(
            array(
                'id' => 1,
                'name' => 'testItem1'
            ),
            array(
                'id' => 2,
                'name' => 'testItem2'
            )
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function create($request)
    {
        return array(
            'id' => rand()
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function update($request)
    {
        return array(
            'id' => $request['id']
        );
    }

    /**
     * @param array $request
     * @return array
     */
    public function remove($request)
    {
        return array(
            'id' => $request['id']
        );
    }
}
