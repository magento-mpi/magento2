<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_TestModule2_Service_Svc1V1 implements Mage_TestModule2_Service_Svc1InterfaceV1
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
            array('Hello' => 'World')
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
