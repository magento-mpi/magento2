<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapper;

class CompositeTest extends \PHPUnit_Framework_TestCase
{
    public function testMap()
    {
        $mapperOne = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapperInterface'
        );

        $mapperTwo = $this->getMock(
            '\Magento\Catalog\Service\V1\Product\Link\Data\ProductLink\DataMapperInterface'
        );

        $model = new Composite([$mapperOne, $mapperTwo]);
        $originData = ['some' => 'test', 'data' => '.'];
        $firstModification = ['some' => 'test', 'data' => '.', 'first' => 'modification'];
        $secondModification = ['some' => 'test', 'data' => '.', 'first' => 'modification', 'second' => 'modification'];

        $mapperOne->expects($this->once())
            ->method('map')
            ->with($originData)
            ->will($this->returnValue($firstModification));

        $mapperTwo->expects($this->once())
            ->method('map')
            ->with($firstModification)
            ->will($this->returnValue($secondModification));

        $this->assertEquals($secondModification, $model->map($originData));
    }
}
