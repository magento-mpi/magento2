<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Eav\Model;

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class AttributeSetRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeSetRepository
     */
    private $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resourceMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $setFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $collectionFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $eavConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $resultBuilderMock;

    protected function setUp()
    {
        $this->resourceMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Set',
            array(),
            array(),
            '',
            false
        );
        $this->setFactoryMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\SetFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->collectionFactoryMock = $this->getMock(
            'Magento\Eav\Model\Resource\Entity\Attribute\Set\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->eavConfigMock = $this->getMock('Magento\Eav\Model\Config', array(), array(), '', false);
        $this->resultBuilderMock = $this->getMock('Magento\Framework\Data\Search\SearchResultsBuilderInterface');
        $this->model = new AttributeSetRepository(
            $this->resourceMock,
            $this->setFactoryMock,
            $this->collectionFactoryMock,
            $this->eavConfigMock//,
            //$this->resultBuilderMock
        );
    }

    public function testGet()
    {
        $attributeSetId = 1;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue($attributeSetId));
        $this->assertEquals($attributeSetMock, $this->model->get($attributeSetId));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with id = 9999
     */
    public function testGetThrowsExceptionIfRequestedAttributeSetDoesNotExist()
    {
        $attributeSetId = 9999;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $this->model->get($attributeSetId);
    }

    public function testSave()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('save')->with($attributeSetMock);
        $this->assertEquals($attributeSetMock, $this->model->save($attributeSetMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotSaveException
     * @expectedExceptionMessage There was an error saving attribute set.
     */
    public function testSaveThrowsExceptionIfGivenEntityCannotBeSaved()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('save')->with($attributeSetMock)->willThrowException(
            new \Exception('Some internal exception message.')
        );
        $this->model->save($attributeSetMock);
    }

    public function testDelete()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock);
        $this->assertTrue($this->model->delete($attributeSetMock));
    }

    /**
     * @expectedException \Magento\Framework\Exception\CouldNotDeleteException
     * @expectedExceptionMessage There was an error deleting attribute set.
     */
    public function testDeleteThrowsExceptionIfGivenEntityCannotBeDeleted()
    {
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock)->willThrowException(
            new \Exception('Some internal exception message.')
        );
        $this->model->delete($attributeSetMock);
    }

    public function testDeleteById()
    {
        $attributeSetId = 1;
        $attributeSetMock = $this->getMock('Magento\Eav\Model\Entity\Attribute\Set', [], [], '', false);
        $attributeSetMock->expects($this->any())->method('getId')->will($this->returnValue($attributeSetId));
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeSetMock));
        $this->resourceMock->expects($this->once())->method('load')->with($attributeSetMock, $attributeSetId, null);
        $this->resourceMock->expects($this->once())->method('delete')->with($attributeSetMock);
        $this->assertTrue($this->model->deleteById($attributeSetId));
    }

    public function testGetList()
    {
        $this->markTestIncomplete('Implement this test when framework provides search result builders');
    }
}
