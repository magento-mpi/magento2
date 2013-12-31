<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\TestModule2\Service;

use Magento\TestModule2\Service\Entity\V1\Item;

class SubsetRestV1 implements \Magento\TestModule2\Service\SubsetRestV1Interface
{
    /**
     * @param int $id
     * @return Item
     */
    public function item($id)
    {
        $response = new Item();
        $response->setId($id);
        return $response;
    }

    /**
     * @return array
     */
    public function items()
    {
        $r1 = new Item();
        $r1->setId('1');
        $r1->setName('testItem1');
        $r2 = new Item();
        $r2->setId('2');
        $r2->setName('testItem2');
        return [$r1, $r2];
    }

    /**
     * @param array $request
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
