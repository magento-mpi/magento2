<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestModule1\Service;

interface AllSoapAndRestV1Interface
{

    /**
     * @param $request
     * @return array
     */
    public function item($request);

    /**
     * TODO: Nested complexType XSD references not supported yet. Need to fix it.
     *
     * @return array
     */
    //public function items();

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

    /**
     * @return array
     */
    public function items();
}
