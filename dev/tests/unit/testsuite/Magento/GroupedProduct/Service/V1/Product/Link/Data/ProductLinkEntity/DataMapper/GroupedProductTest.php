<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\DataMapper;


class GroupedProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers \Magento\GroupedProduct\Service\V1\Product\Link\Data\ProductLinkEntity\DataMapper\GroupedProduct::map
     */
    public function testMap()
    {
        $data = [
            [
                'name' => 'item1',
                'custom_attributes' => ['qty' => ['value' => 5]]
            ],
            [
                'name' => 'item2'
            ]
        ];

        $model = new GroupedProduct();
        $mappedData = $model->map($data);

        $this->assertEquals(5, $mappedData[0]['qty']);
        $this->assertFalse(isset($mappedData[1]['qty']));
    }
}
