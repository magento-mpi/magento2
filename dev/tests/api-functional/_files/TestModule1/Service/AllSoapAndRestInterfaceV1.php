<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_TestModule1_Service_AllSoapAndRestInterfaceV1
{

    /**
     * @param $request
     * @return array
     */
    public function item($request);

    /**
     * @return array
     */
    public function items();

    /**
     * @param $request
     * @return array
     */
    public function create($request);

    /**
     * @param $request
     * @return array
     */
    public function update($request);

}