<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\AttributeGroup;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Product\AttributeGroup\ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $groupListFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $setFactoryMock;

    protected function setUp()
    {
        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->groupListFactory = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->setFactoryMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\SetFactory',
            array('create'),
            array(),
            '',
            false
        );
        $groupBuilder = $helper->getObject('\Magento\Catalog\Service\V1\Data\Eav\AttributeGroupBuilder');
        $this->service = new ReadService($this->groupListFactory, $this->setFactoryMock, $groupBuilder);
    }

    public function testListGroups()
    {
        $groupList = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group\Collection',
            array(),
            array(),
            '',
            false
        );
        $this->groupListFactory->expects($this->once())->method('create')->will($this->returnValue($groupList));
        $attributeSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $attributeSetMock->expects($this->once())->method('load')->with(1)->will($this->returnSelf());
        $attributeSetMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $item1 = new \Magento\Framework\Object(array('id' => 1, 'attribute_group_name' => 'First'));
        $item2 = new \Magento\Framework\Object(array('id' => 2, 'attribute_group_name' => 'Second'));
        $groupList->expects($this->once())->method('getItems')->will($this->returnValue(array($item1, $item2)));
        $result = $this->service->getList(1);
        $this->assertCount(2, $result);
        $this->assertInstanceOf('\Magento\Catalog\Service\V1\Data\Eav\AttributeGroup', $result[0]);
        $this->assertInstanceOf('\Magento\Catalog\Service\V1\Data\Eav\AttributeGroup', $result[1]);
        $this->assertEquals('First', $result[0]->getName());
        $this->assertEquals('Second', $result[1]->getName());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testListGroupsWrongAttributeSet()
    {
        $attributeSetMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $attributeSetMock->expects($this->once())->method('load')->with(1)->will($this->returnSelf());
        $attributeSetMock->expects($this->once())->method('getId')->will($this->returnValue(null));

        $this->service->getList(1);
    }
}
