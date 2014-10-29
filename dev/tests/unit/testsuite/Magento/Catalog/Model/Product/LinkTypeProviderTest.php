<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

use \Magento\Catalog\Api\Data\ProductLinkTypeInterface as LinkType;
use \Magento\Catalog\Api\Data\ProductLinkAttributeInterface as LinkAttribute;

class LinkTypeProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\LinkTypeProvider
     */
    protected $model;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkTypeInterfaceBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkTypeBuilderMock;

    /**
     * @var \Magento\Catalog\Api\Data\ProductLinkAttributeInterfaceBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkAttributeBuilderMock;

    /**
     * @var \Magento\Catalog\Model\Product\LinkFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $linkFactoryMock;

    /**
     * @var Array
     */
    protected $linkTypes;

    protected function setUp()
    {
        $this->linkTypeBuilderMock = $this->getMock(
            '\Magento\Catalog\Api\Data\ProductLinkTypeInterfaceBuilder',
            ['create', 'populateWithArray']
        );
        $this->linkAttributeBuilderMock = $this->getMock(
            '\Magento\Catalog\Api\Data\ProductLinkAttributeInterfaceBuilder',
            ['populateWithArray']
        );
        $this->linkFactoryMock = $this->getMock('\Magento\Catalog\Model\Product\LinkFactory', ['create']);
        $this->linkTypes = [
            'test_product_link_1' => 'test_code_1',
            'test_product_link_2' => 'test_code_2',
            'test_product_link_3' => 'test_code_3'
        ];
        $this->model = new \Magento\Catalog\Model\Product\LinkTypeProvider(
            $this->linkTypeBuilderMock,
            $this->linkAttributeBuilderMock,
            $this->linkFactoryMock,
            $this->linkTypes
        );
    }

    public function testGetItems()
    {
        $expectedResult = [];
        $objectMocks = [];
        foreach ($this->linkTypes as $type => $typeCode) {
            $value = [LinkType::KEY => $type, LinkType::VALUE => $typeCode];
            $objectMock = $this->getMock('\Magento\Framework\Object', ['create'], [], '', false);
            $objectMock->expects($this->once())->method('create')->willReturn($value);
            $objectMocks[] = $objectMock;
            $expectedResult[] = $value;
        }
        $valueMap = function ($expectedResult, $objectMocks) {
            $output = [];
            foreach ($expectedResult as $key => $result) {
                $output[] = [$expectedResult[$key], $objectMocks[$key]];
            }
            return $output;
        };
        $this->linkTypeBuilderMock->expects($this->any())->method('populateWithArray')->will($this->returnValueMap(
            $valueMap($expectedResult, $objectMocks)
        ));
        $this->assertEquals($expectedResult, $this->model->getItems());
    }

    /**
     * @dataProvider getItemAttributesDataProvider
     */
    public function testGetItemAttributes($type, $typeId)
    {
        $attributes = [
            ['code' => 'test_code_1', 'type' => 'test_type_1']
        ];
        $expectedResult = [
            [LinkAttribute::KEY => $attributes[0]['code'], LinkAttribute::VALUE => $attributes[0]['type']]
        ];
        $objectMock = $this->getMock('\Magento\Framework\Object', ['create'], [], '', false);
        $objectMock->expects($this->once())->method('create')->willReturn(
            [LinkAttribute::KEY => $attributes[0]['code'], LinkAttribute::VALUE => $attributes[0]['type']]
        );
        $linkMock = $this->getMock('\Magento\Catalog\Model\Product\Link', ['getAttributes'], [], '', false);
        $linkMock->expects($this->once())->method('getAttributes')->willReturn($attributes);
        $this->linkFactoryMock->expects($this->once())->method('create')->with($typeId)->willReturn($linkMock);
        $this->linkAttributeBuilderMock->expects($this->any())->method('populateWithArray')->willReturn($objectMock);
        $this->assertEquals($expectedResult, $this->model->getItemAttributes($type));
    }

    public function getItemAttributesDataProvider()
    {
        return [
            ['test_product_link_2', ['data' => ['link_type_id' => 'test_code_2']]],
            ['null_product', ['data' => ['link_type_id' => null]]]
        ];
    }
}
