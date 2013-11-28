<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

interface SubsetRestV1Interface
{
    /**
     * Return a single item.
     *
     * @param int $id
     * @return Item
     */
    public function item($id);

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
