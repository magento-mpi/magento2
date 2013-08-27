<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_TestModule2_Service_NoWebApiXmlV1Interface
{
    /**
     * Return a single item.
     *
     * @param $request array
     * @return array
     */
    public function item($request);

    /**
     * TODO: Nested complexType XSD references not supported yet. Need to fix it.
     * Return multiple items.
     *
     * @return array
     */
    //public function items();

    /**
     * Create an item.
     *
     * @param $request array
     * @return array
     */
    public function create($request);

    /**
     * Update an item.
     *
     * @param $request array
     * @return array
     */
    public function update($request);

    /**
     * Delete an item.
     *
     * @param $request array
     * @return array
     */
    public function remove($request);
}
