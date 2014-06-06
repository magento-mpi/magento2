<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Service\V1\Product\Attribute\Media;

class ReadServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Service\V1\Product\Attribute\Media\ReadService
     */
    protected $service;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $collectionFactoryMock;

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
    protected $setMock;

    /**
     * @var int attribute set id to use in tests
     */
    protected $attributeSetId = 100123;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeCollectionMock;

    protected function setUp()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->collectionFactoryMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory',
            array('create', '__wakeup'),
            array(),
            '',
            false
        );

        $this->attributeCollectionMock = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Attribute\Collection',
            array(),
            array(),
            '',
            false
        );

        $mediaImageBuilder = $objectHelper->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImageBuilder'
        );

        $this->setFactoryMock = $this->getMock(
            'Magento\Eav\Model\Entity\Attribute\SetFactory',
            array('create', '__wakeup'),
            array(),
            '',
            false
        );

        $this->eavConfigMock = $this->getMock(
            '\Magento\Eav\Model\Config', array('getEntityType', 'getId'), array(), '', false
        );

        $this->service = $objectHelper->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\Media\ReadService',
            array(
                'collectionFactory' => $this->collectionFactoryMock,
                'setFactory' => $this->setFactoryMock,
                'eavConfig' => $this->eavConfigMock,
                'builder' => $mediaImageBuilder,
            )
        );

        $this->setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('getEntityTypeId', 'load', 'getId', '__wakeup'),
            array(),
            '',
            false
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetTypesForAbsentId()
    {
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->setMock));

        $this->setMock->expects($this->once())
            ->method('load')
            ->with($this->attributeSetId)
            ->will($this->returnSelf());

        $this->setMock->expects($this->once())->method('getId')->will($this->returnValue(null));
        $this->service->getTypes($this->attributeSetId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testGetTypesForWrongEntityType()
    {
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->setMock));

        $this->setMock->expects($this->once())
            ->method('load')
            ->with($this->attributeSetId)
            ->will($this->returnSelf());

        $this->setMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->will($this->returnSelf());
        $this->eavConfigMock->expects($this->once())->method('getId')->will($this->returnValue(1));
        $this->setMock->expects($this->once())->method('getEntityTypeId')->will($this->returnValue(4));

        $this->service->getTypes($this->attributeSetId);
    }

    public function testGetTypesPositive()
    {
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->setMock));

        $this->setMock->expects($this->once())
            ->method('load')
            ->with($this->attributeSetId)
            ->will($this->returnSelf());

        $this->setMock->expects($this->once())->method('getId')->will($this->returnValue(1));

        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->will($this->returnSelf());
        $this->eavConfigMock->expects($this->once())->method('getId')->will($this->returnValue(4));
        $this->setMock->expects($this->once())->method('getEntityTypeId')->will($this->returnValue(4));

        $this->collectionFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->attributeCollectionMock));
        $this->attributeCollectionMock->expects($this->once())->method('setAttributeSetFilter')->with($this->attributeSetId);
        $this->attributeCollectionMock->expects($this->once())
            ->method('setFrontendInputTypeFilter')
            ->with('media_image');
        $attributeMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Eav\Attribute',
            array('getFrontendLabel', 'getData', 'getIsGlobal', 'isScopeWebsite', 'isScopeStore', '__wakeup'),
            array(),
            '',
            false
        );
        $attributeMock->expects($this->once())->method('getFrontendLabel')->will($this->returnValue('coolLabel'));
        $attributeMock->expects($this->any())->method('getData')->will($this->returnArgument(0));
        $attributeMock->expects($this->once())->method('getIsGlobal')->will($this->returnValue(false));
        $attributeMock->expects($this->once())->method('isScopeWebsite')->will($this->returnValue(false));
        $attributeMock->expects($this->once())->method('isScopeStore')->will($this->returnValue(true));

        $items = array($attributeMock);
        $this->attributeCollectionMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($items));

        $attributes = $this->service->getTypes($this->attributeSetId);
        $this->assertEquals(1, count($attributes));
        /** @var \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImage $resultAttribute */
        $resultAttribute = reset($attributes);
        $this->assertEquals('coolLabel', $resultAttribute->getFrontendLabel());
        $this->assertEquals('attribute_code', $resultAttribute->getCode());
        $this->assertEquals(true, $resultAttribute->getIsUserDefined());
        $this->assertEquals('Store View', $resultAttribute->getScope());
    }
}