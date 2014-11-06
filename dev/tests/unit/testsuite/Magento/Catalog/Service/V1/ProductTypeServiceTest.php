<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductTypeServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductTypeService
     */
    private $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $typeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $typeBuilderMock;

    protected function setUp()
    {
        $this->typeConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->typeBuilderMock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\ProductTypeBuilder',
            array(),
            array(),
            '',
            false
        );
        $this->service = new ProductTypeService(
            $this->typeConfigMock,
            $this->typeBuilderMock
        );
    }

    public function testGetProductTypes()
    {
        $simpleProductType = array(
            'name' => 'simple',
            'label' => 'Simple Product',
        );
        $productTypeData = array(
            'simple' => $simpleProductType,
        );
        $productTypeMock = $this->getMock(
            'Magento\Catalog\Service\V1\Data\ProductType',
            array(),
            array(),
            '',
            false
        );
        $this->typeConfigMock->expects($this->any())->method('getAll')->will($this->returnValue($productTypeData));
        $this->typeBuilderMock->expects($this->once())
            ->method('setName')
            ->with($simpleProductType['name'])
            ->will($this->returnSelf());
        $this->typeBuilderMock->expects($this->once())
            ->method('setLabel')
            ->with($simpleProductType['label'])
            ->will($this->returnSelf());
        $this->typeBuilderMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($productTypeMock));

        $productTypes = $this->service->getProductTypes();
        $this->assertCount(1, $productTypes);
        $this->assertContains($productTypeMock, $productTypes);
    }
}
