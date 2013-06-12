<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Mage_TestModule2_Service_SubsetRestInterfaceV1
{
    /**
     * Return a single item.
     *
     * @param $request array
     * @return array
     */
    public function item($request);

    /**
     * Return multiple items.
     *
     * @param $request array
     * @return array
     */
    public function items($request);

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
