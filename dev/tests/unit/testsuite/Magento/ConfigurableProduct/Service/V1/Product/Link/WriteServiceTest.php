<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;


use Magento\TestFramework\Helper\ObjectManager;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ConfigurableProduct\Service\V1\Product\Link\WriteService
     */
    private $model;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    private $product;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable|\PHPUnit_Framework_MockObject_MockObject
     */
    private $productType;

    protected function setUp()
    {
        $this->productType = $this->getMockBuilder('Magento\ConfigurableProduct\Model\Product\Type\Configurable')
            ->setMethods(['getUsedProducts'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->setMethods(['getTypeInstance', 'save', 'getTypeId', 'addData', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->product
            ->expects($this->any())
            ->method('getTypeInstance')
            ->will($this->returnValue($this->productType));

        $productRepository = $this->getMockBuilder('Magento\Catalog\Model\ProductRepository')
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $productRepository->expects($this->any())->method('get')->will($this->returnValue($this->product));

        $this->model = (new ObjectManager($this))->getObject(
            'Magento\ConfigurableProduct\Service\V1\Product\Link\WriteService',
            [
                'productRepository' => $productRepository
            ]
        );
    }

    public function testRemoveChild()
    {
        $productSku = 'configurable';
        $childSku = 'simple_10';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));

        $option = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $option->expects($this->any())->method('getSku')->will($this->returnValue($childSku));
        $option->expects($this->any())->method('getId')->will($this->returnValue(10));
        $this->productType->expects($this->once())->method('getUsedProducts')
            ->will($this->returnValue([$option]));

        $this->product->expects($this->once())->method('addData')->with(['associated_product_ids' => array()]);
        $this->product->expects($this->once())->method('save');
        $this->assertTrue($this->model->removeChild($productSku, $childSku));
    }

    /**
     * @expectedException \Magento\Webapi\Exception
     * @expectedExceptionCode 403
     */
    public function testRemoveChildForbidden()
    {
        $productSku = 'configurable';
        $childSku = 'simple_10';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE));
        $this->model->removeChild($productSku, $childSku);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveChildInvalidChildSku()
    {
        $productSku = 'configurable';
        $childSku = 'simple_10';

        $this->product
            ->expects($this->any())
            ->method('getTypeId')
            ->will($this->returnValue(\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE));

        $option = $this->getMockBuilder('\Magento\Catalog\Model\Product')
            ->setMethods(['getSku', 'getId', '__wakeup'])
            ->disableOriginalConstructor()
            ->getMock();
        $option->expects($this->any())->method('getSku')->will($this->returnValue($childSku . '_invalid'));
        $option->expects($this->any())->method('getId')->will($this->returnValue(10));
        $this->productType->expects($this->once())->method('getUsedProducts')
            ->will($this->returnValue([$option]));

        $this->model->removeChild($productSku, $childSku);
    }
}
