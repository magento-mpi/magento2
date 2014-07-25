<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Options;

use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Product\Options\WriteService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productRepository;

    /**
     * @var Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attributeCollection;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $attribute;

    protected function setUp()
    {
        $this->productType = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable')
            ->setMethods(['getConfigurableAttributeCollection'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getTypeId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attributeCollection = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable\Attribute\Collection'
        )
            ->setMethods(['getItemById'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->attribute = $this->getMockBuilder(
            'Magento\ConfigurableProduct\Model\Product\Type\Configurable\Attribute'
        )
            ->setMethods(['delete', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\ConfigurableProduct\Service\V1\Product\Options\WriteService',
            [
                'productRepository' => $this->productRepository,
                'productType' => $this->productType
            ]
        );
    }

    public function testRemove()
    {
        $productSku = 'productSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(Configurable::TYPE_CODE));

        $this->productType->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->attributeCollection));

        $this->attributeCollection->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue($this->attribute));

        $this->attribute->expects($this->once())->method('delete');

        $this->assertTrue($this->model->remove($productSku, $optionId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveNoSuchEntityException()
    {
        $productSku = 'productSku';
        $optionId = 3;

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(Configurable::TYPE_CODE));

        $this->productType->expects($this->once())->method('getConfigurableAttributeCollection')
            ->with($this->equalTo($this->product))
            ->will($this->returnValue($this->attributeCollection));

        $this->attributeCollection->expects($this->once())->method('getItemById')
            ->with($this->equalTo($optionId))
            ->will($this->returnValue(null));

        $this->model->remove($productSku, $optionId);
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     */
    public function testRemoveWebApiException()
    {
        $productSku = 'productSku';

        $this->productRepository->expects($this->once())->method('get')
            ->with($this->equalTo($productSku))
            ->will($this->returnValue($this->product));

        $this->product->expects($this->once())->method('getTypeId')
            ->will($this->returnValue(Type::TYPE_SIMPLE));
        $this->product->expects($this->once())->method('getSku')
            ->will($this->returnValue($productSku));

        $this->model->remove($productSku, 3);
    }
}
