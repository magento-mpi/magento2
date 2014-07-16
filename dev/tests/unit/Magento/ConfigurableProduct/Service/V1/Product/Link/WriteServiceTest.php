<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Service\V1\Product\Link;

use Magento\ConfigurableProduct\Model\Resource\Product\Type\Configurable;

class WriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Catalog\Model\ProductRepository|\PHPUnit_Framework_MockObject_MockObject */
    protected $productRepository;
    /** @var Configurable|\PHPUnit_Framework_MockObject_MockObject */
    protected $configurableType;
    /** @var \Magento\TestFramework\Helper\ObjectManager */
    protected $objectManagerHelper;
    /** @var WriteService */
    protected $object;

    public function setUp()
    {
        $this->productRepository = $this->getMockBuilder('Magento\\Catalog\\Model\\ProductRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->configurableType =
            $this->getMockBuilder('Magento\\ConfigurableProduct\\Model\\Resource\\Product\\Type\\Configurable')
                ->disableOriginalConstructor()->getMock();
        $this->objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->object = $this->objectManagerHelper->getObject(
            'Magento\\ConfigurableProduct\\Service\\V1\\Product\\Link\\WriteService',
            array('productRepository' => $this->productRepository, 'configurableType' => $this->configurableType)
        );
    }

    public function testAddChild()
    {
        $productSku = 'configurable-sku';
        $childSku = 'simple-sku';

        $configurable = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $configurable->expects($this->any())->method('getId')->will($this->returnValue(666));

        $simplee = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()
            ->getMock();

        $simplee->expects($this->any())->method('getId')->will($this->returnValue(999));

        $this->productRepository->expects($this->at(0))->method('get')->with($productSku)->will(
            $this->returnValue($configurable)
        );

        $this->productRepository->expects($this->at(1))->method('get')->with($childSku)->will(
            $this->returnValue($simplee)
        );

        $this->configurableType->expects($this->once())->method('getChildrenIds')->with(666)
            ->will(
                $this->returnValue([0 => [1, 2, 3]])
            );
        $configurable->expects($this->once())->method('__call')->with('setAssociatedProductIds',[[1, 2, 3, 999]]);
        $configurable->expects($this->once())->method('save');

        $this->assertTrue(true, $this->object->addChild($productSku, $childSku));
    }
}
