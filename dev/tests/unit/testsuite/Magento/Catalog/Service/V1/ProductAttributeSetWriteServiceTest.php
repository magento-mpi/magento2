<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1;

use Magento\Catalog\Service\V1\Data\Eav\AttributeSet;

class ProductAttributeSetWriteServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $setFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $eavConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityTypeMock;

    /**
     * @var \Magento\Catalog\Service\V1\ProductAttributeSetWriteService
     */
    protected $service;

    /**
     * @var int default attribute set id
     */
    protected $defaultSetId = 4;

    protected function setUp()
    {
        $this->setFactoryMock = $this->getMock('\Magento\Eav\Model\Entity\Attribute\SetFactory',
            array('create'), array(), '', false);
        $this->eavConfigMock = $this->getMock('\Magento\Eav\Model\Config', array(), array(), '', false);
        $this->entityTypeMock = $this->getMock('\Magento\Eav\Model\Entity\Type', array(), array(), '', false);
        $this->eavConfigMock->expects($this->any())->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->will($this->returnValue($this->entityTypeMock));
        $this->service = new ProductAttributeSetWriteService(
            $this->setFactoryMock,
            $this->eavConfigMock
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateWithExistingId()
    {
        $setDataMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended',
            array(),
            array(),
            '',
            false
        );
        $setDataMock->expects($this->any())->method('getId')->will($this->returnValue(100));

        $this->service->create($setDataMock);
    }

    /**
     * @expectedException \Magento\Eav\Exception
     */
    public function testCreateWithEmptyName()
    {
        $setDataMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended',
            array(),
            array(),
            '',
            false
        );
        $setDataMock->expects($this->any())->method('getId')->will($this->returnValue(null));
        $setDataMock->expects($this->any())->method('getName')->will($this->returnValue(null));
        $setDataMock->expects($this->any())->method('getSortOrder')->will($this->returnValue(20));

        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'load', '__wakeup'),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($setMock));
        $exception = new \Magento\Eav\Exception('empty name');
        $setMock->expects($this->once())->method('validate')->will($this->throwException($exception));
        $setMock->expects($this->never())->method('save');

        $this->service->create($setDataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testCreateWithNoSkeletonId()
    {
        $setId = 123321;
        $setDataMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended',
            array(),
            array(),
            '',
            false
        );
        $setDataMock->expects($this->any())->method('getId')->will($this->returnValue(null));
        $setDataMock->expects($this->any())->method('getName')->will($this->returnValue('cool attribute set'));
        $setDataMock->expects($this->any())->method('getSortOrder')->will($this->returnValue(20));
        $setDataMock->expects($this->any())->method('getSkeletonId')->will($this->returnValue(null));

        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'getData', 'load', '__wakeup'),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($setMock));
        $setMock->expects($this->once())->method('validate')->will($this->returnValue(true));
        $setMock->expects($this->once())->method('save');

        $this->assertEquals($setId, $this->service->create($setDataMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testCreateWithAbsentSkeleton()
    {
        $setId = 123321;
        $setDataMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended',
            array(),
            array(),
            '',
            false
        );
        $setDataMock->expects($this->any())->method('getId')->will($this->returnValue(null));
        $setDataMock->expects($this->any())->method('getName')->will($this->returnValue('cool attribute set'));
        $setDataMock->expects($this->any())->method('getSortOrder')->will($this->returnValue(20));
        $setDataMock->expects($this->any())->method('getSkeletonId')->will($this->returnValue($this->defaultSetId));

        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'getData', 'load', '__wakeup'),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->exactly(2))->method('create')->will($this->returnValue($setMock));
        $setMock->expects($this->once())->method('validate')->will($this->returnValue(true));
        $setMock->expects($this->once())->method('save');
        $skeletonSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'getData', 'load', '__wakeup'),
            array(),
            '',
            false
        );
        $skeletonSetMock->expects($this->once())->method('getData')->will($this->returnValue(array()));
        $setMock->expects($this->once())->method('load')
            ->with($this->defaultSetId)
            ->will($this->returnValue($skeletonSetMock));

        $this->assertEquals($setId, $this->service->create($setDataMock));
    }

    public function testCreatePositive()
    {
        $setId = 123321;
        $setDataMock = $this->getMock(
            '\Magento\Catalog\Service\V1\Data\Eav\AttributeSetExtended',
            array(),
            array(),
            '',
            false
        );
        $setDataMock->expects($this->any())->method('getId')->will($this->returnValue(null));
        $setDataMock->expects($this->any())->method('getName')->will($this->returnValue('cool attribute set'));
        $setDataMock->expects($this->any())->method('getSortOrder')->will($this->returnValue(20));
        $setDataMock->expects($this->any())->method('getSkeletonId')->will($this->returnValue($this->defaultSetId));

        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'load', 'initFromSkeleton', '__wakeup'),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->exactly(2))->method('create')->will($this->returnValue($setMock));
        $setMock->expects($this->once())->method('validate')->will($this->returnValue(true));
        $setMock->expects($this->exactly(2))->method('save');
        $setMock->expects($this->once())->method('getId')->will($this->returnValue($setId));
        $skeletonSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('setData', 'validate', 'save', 'getId', 'load', 'getData', '__wakeup'),
            array(),
            '',
            false
        );
        $setMock->expects($this->once())->method('load')
            ->with($this->defaultSetId)
            ->will($this->returnValue($skeletonSetMock));
        $skeletonSetMock->expects($this->once())->method('getData')->will($this->returnValue(array(1, 2, 3)));
        $setMock->expects($this->once())->method('initFromSkeleton')->with($this->defaultSetId);

        $this->assertEquals($setId, $this->service->create($setDataMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testRemoveInvalidId()
    {
        $this->setFactoryMock->expects($this->never())->method('create');
        $this->service->remove('absent id');
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testRemoveAbsentSet()
    {
        $id = 145678;
        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($setMock));
        $setMock->expects($this->once())->method('load')->with($id)->will($this->returnSelf());
        $setMock->expects($this->once())->method('getData')->will($this->returnValue(null));
        $setMock->expects($this->never())->method('delete');
        $this->service->remove($id);
    }

    public function testRemovePositive()
    {
        $id = 456;
        $setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($setMock));
        $setMock->expects($this->once())->method('load')->with($id)->will($this->returnSelf());
        $setMock->expects($this->once())->method('getData')->will($this->returnValue(array(5, 6, 7)));
        $setMock->expects($this->once())->method('delete');

        $this->service->remove($id);
    }

    public function testUpdate()
    {
        $data = array(
            AttributeSet::ID => 4,
            AttributeSet::NAME => 'Test Attribute Set',
            AttributeSet::ORDER => 200,
        );
        $attributeSetDataMock = $this->getMock('Magento\Catalog\Service\V1\Data\Eav\AttributeSet',
            array(), array(), '', false);
        $attributeSetDataMock->expects($this->any())->method('getId')
            ->will($this->returnValue($data[AttributeSet::ID]));
        $attributeSetDataMock->expects($this->any())->method('getName')
            ->will($this->returnValue($data[AttributeSet::NAME]));
        $attributeSetDataMock->expects($this->any())->method('getSortOrder')
            ->will($this->returnValue($data[AttributeSet::ORDER]));

        $attributeSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('load', 'getId', 'setAttributeSetName', 'setSortOrder', '__wakeup', 'getEntityTypeId', 'save'),
            array(),
            '',
            false
        );
        $entityTypeId = 4;
        $this->entityTypeMock->expects($this->once())->method('getId')->will($this->returnValue($entityTypeId));

        $this->setFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($attributeSetMock));
        $attributeSetMock->expects($this->once())
            ->method('load')
            ->with($data[AttributeSet::ID])
            ->will($this->returnSelf());
        $attributeSetMock->expects($this->any())
            ->method('getEntityTypeId')
            ->will($this->returnValue($entityTypeId));
        $attributeSetMock->expects($this->any())->method('getId')
            ->will($this->returnValue($data[AttributeSet::ID]));
        $attributeSetMock->expects($this->once())->method('setAttributeSetName')->with($data[AttributeSet::NAME])
            ->will($this->returnSelf());
        $attributeSetMock->expects($this->once())->method('setSortOrder')->with($data[AttributeSet::ORDER])
            ->will($this->returnSelf());
        $attributeSetMock->expects($this->once())->method('save')
            ->will($this->returnSelf());
        $this->assertEquals($data[AttributeSet::ID], $this->service->update($attributeSetDataMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     * @expectedExceptionMessage id is a required field.
     */
    public function testUpdateThrowsExceptionIfAttributeSetIdIsNotSpecified()
    {
        $data = array(
            AttributeSet::ID => null,
            AttributeSet::NAME => 'Test Attribute Set',
            AttributeSet::ORDER => 200,
        );
        $attributeSetDataMock = $this->getMock('Magento\Catalog\Service\V1\Data\Eav\AttributeSet',
            array(), array(), '', false);
        $attributeSetDataMock->expects($this->any())->method('getId')
            ->will($this->returnValue($data[AttributeSet::ID]));
        $this->service->update($attributeSetDataMock);
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 9999
     */
    public function testUpdateThrowsExceptionIfAttributeSetIdIsInvalid()
    {
        $entityTypeId = 4;
        $data = array(
            AttributeSet::ID => 9999,
            AttributeSet::NAME => 'Test Attribute Set',
            AttributeSet::ORDER => 200,
        );
        $attributeSetDataMock = $this->getMock('Magento\Catalog\Service\V1\Data\Eav\AttributeSet',
            array(), array(), '', false);
        $attributeSetDataMock->expects($this->any())->method('getId')
            ->will($this->returnValue($data[AttributeSet::ID]));

        $attributeSetMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->entityTypeMock->expects($this->once())->method('getId')->will($this->returnValue($entityTypeId));

        $this->setFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($attributeSetMock));
        $attributeSetMock->expects($this->once())
            ->method('load')
            ->with($data[AttributeSet::ID])
            ->will($this->returnSelf());
        $attributeSetMock->expects($this->any())
            ->method('getEntityTypeId')
            ->will($this->returnValue($entityTypeId));
        $attributeSetMock->expects($this->any())->method('getId');
        $attributeSetMock->expects($this->never())->method('setAttributeSetName');
        $attributeSetMock->expects($this->never())->method('setSortOrder');
        $attributeSetMock->expects($this->never())->method('save');
        $this->service->update($attributeSetDataMock);
    }
}
