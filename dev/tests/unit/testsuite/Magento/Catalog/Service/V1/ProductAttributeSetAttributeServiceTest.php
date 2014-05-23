<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

class ProductAttributeSetAttributeServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ProductAttributeSetAttributeService
     */
    private $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeGroupMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeSetMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attrResourceMock;

    protected function setUp()
    {
        $this->attributeMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute',
            array(
                'getId', 'setId', 'setEntityTypeId', 'setAttributeSetId', 'load',
                'setAttributeGroupId', 'setSortOrder', 'loadEntityAttributeIdBySet', '__sleep', '__wakeup'
            ),
            array(), '', false
        );
        $this->attributeGroupMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Group', array(), array(), '', false
        );
        $this->attributeSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set', array(), array(), '', false
        );
        $this->attrResourceMock = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute', array(), array(), '', false
        );

        $this->service = new ProductAttributeSetAttributeService(
            $this->attributeMock,
            $this->attributeGroupMock,
            $this->attributeSetMock,
            $this->attrResourceMock
        );
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::__construct
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     */
    public function testAddAttribute()
    {
        $data = [
            'attribute_id'       => 1,
            'attribute_group_id' => 1,
            'sort_order'         => 1
        ];

        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray($data)->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $objectMock->expects($this->any())->method('getData')->will($this->returnValue(1));

        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeGroupMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));

        $this->attributeMock->expects($this->once())->method('setId')->with(1);
        $this->attributeMock->expects($this->once())->method('setEntityTypeId')->with(4);
        $this->attributeMock->expects($this->once())->method('setAttributeSetId')->with(1);
        $this->attributeMock->expects($this->once())->method('setAttributeGroupId')->with(1);
        $this->attributeMock->expects($this->once())->method('setSortOrder')->with(1);
        $this->attributeMock->expects($this->once())
            ->method('loadEntityAttributeIdBySet')->will($this->returnValue($objectMock));
        $this->attrResourceMock->expects($this->once())->method('saveInSetIncluding')->with($this->attributeMock);

        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute set does not exist
     */
    public function testAddAttributeWithWrongAttributeSet()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray([])->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute group does not exist
     */
    public function testAddAttributeWithWrongAttributeGroup()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray(['attribute_group'])->create();

        $attributeSetMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($attributeSetMock));
        $attributeGroupMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeGroupMock->expects($this->once())->method('load')
            ->will($this->returnValue($attributeGroupMock));

        $this->service->addAttribute(1, $attributeDataObject);
    }

    /**
     * @covers \Magento\Catalog\Service\V1\ProductAttributeSetAttributeService::addAttribute
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage Attribute does not exist
     */
    public function testAddAttributeWithWrongAttribute()
    {
        $builder = new \Magento\Catalog\Service\V1\Data\Eav\AttributeSet\AttributeBuilder();
        $attributeDataObject = $builder->populateWithArray(['attribute_group'])->create();

        $objectMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $objectMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $this->attributeSetMock->expects($this->once())->method('load')->will($this->returnValue($objectMock));
        $this->attributeGroupMock->expects($this->once())->method('load') ->will($this->returnValue($objectMock));
        $attributeMock = $this->getMock('\Magento\Framework\Object', array(), array(), '', false);
        $this->attributeMock->expects($this->once())->method('load')->will($this->returnValue($attributeMock));

        $this->service->addAttribute(1, $attributeDataObject);
    }
}
