<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model;

class ProductTypeListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductTypeList
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $typeConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $builderMock;

    protected function setUp()
    {
        $this->typeConfigMock = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');
        $this->builderMock = $this->getMock(
            'Magento\Catalog\Api\Data\ProductTypeDataBuilder',
            array('create', 'populateWithArray'),
            array(),
            '',
            false
        );
        $this->model = new ProductTypeList(
            $this->typeConfigMock,
            $this->builderMock
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
        $productTypeMock = $this->getMock('Magento\Catalog\Api\Data\ProductTypeInterface');
        $this->typeConfigMock->expects($this->any())->method('getAll')->will($this->returnValue($productTypeData));
        $this->builderMock->expects($this->once())
            ->method('populateWithArray')
            ->with(array(
                'name' => $simpleProductType['name'],
                'label' => $simpleProductType['label'],
            ))->willReturnSelf();

        $this->builderMock->expects($this->once())->method('create')->willReturn($productTypeMock);
        $productTypes = $this->model->getProductTypes();
        $this->assertCount(1, $productTypes);
        $this->assertContains($productTypeMock, $productTypes);
    }
}
