<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

interface SubsetRestV1Interface
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
     * @return array
     */
    public function items();

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
