<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Data;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;

class ProductMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProductMapper */
    protected $object;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    protected function setUp()
    {
        $this->objectManagerHelper = new ObjectManagerHelper($this);
    }

    public function testToModel()
    {
        $productFactory = $this->getMockBuilder('Magento\Catalog\Model\ProductFactory')
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();

        /** @var \Magento\Catalog\Service\V1\Data\ProductMapper $productMapper */
        $productMapper = $this->objectManagerHelper->getObject(
            'Magento\Catalog\Service\V1\Data\ProductMapper',
            ['productFactory' => $productFactory]
        );

        $product = $this->getMockBuilder('Magento\Catalog\Service\V1\Data\Product')
               ->disableOriginalConstructor()
               ->getMock();
        $product->expects($this->once())->method('__toArray')->will($this->returnValue([
            'test_code' => 'test_value',
        ]));

        /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject $productModel */
        $productModel = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $productFactory->expects($this->once())->method('create')->will($this->returnValue($productModel));

        $this->assertEquals($productModel, $productMapper->toModel($product));

    }
}