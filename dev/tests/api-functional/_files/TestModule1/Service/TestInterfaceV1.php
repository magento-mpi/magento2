<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

interface Mage_TestModule1_Service_TestInterfaceV1
{

    /**
     * @param $request
     * @return array
     */
    public function item($request);

    /**
     * @param $request
     * @return array
     */
    public function items($request);

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