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

    protected function setUp()
    {
        $this->groupListFactory = $this->getMock(
            '\Magento\Eav\Model\Resource\Entity\Attribute\Group\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $groupBuilder = new \Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroupBuilder();
        $this->service = new ReadService($this->groupListFactory, $groupBuilder);
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
        $item1 = new \Magento\Framework\Object(array('id' => 1, 'attribute_group_name' => 'First'));
        $item2 = new \Magento\Framework\Object(array('id' => 2, 'attribute_group_name' => 'Second'));
        $groupList->expects($this->once())->method('getItems')->will($this->returnValue(array($item1, $item2)));
        $result = $this->service->getList(1);
        $this->assertCount(2, $result);
        $this->assertInstanceOf('\Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup', $result[0]);
        $this->assertInstanceOf('\Magento\Catalog\Service\V1\Product\Data\Eav\AttributeGroup', $result[1]);
        $this->assertEquals('First', $result[0]->getName());
        $this->assertEquals('Second', $result[1]->getName());
    }
}
