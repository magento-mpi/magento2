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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $attributeFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $mediaGalleryMock;

    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $objectHelper;

    protected function setUp()
    {
        $this->objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

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

        $mediaImageBuilder = $this->objectHelper->getObject(
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

        $this->productFactoryMock = $this->getMock(
            'Magento\Catalog\Model\ProductFactory',
            array('create', '__wakeup'),
            array(),
            '',
            false
        );

        $this->attributeFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Eav\AttributeFactory',
            array('create', '__wakeup'),
            array(),
            '',
            false
        );

        $this->mediaGalleryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\Backend\Media',
            array(),
            array(),
            '',
            false
        );

        $builder = $this->objectHelper->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryBuilder'
        );

        $this->service = $this->objectHelper->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\Media\ReadService',
            array(
                'collectionFactory' => $this->collectionFactoryMock,
                'setFactory' => $this->setFactoryMock,
                'eavConfig' => $this->eavConfigMock,
                'builder' => $mediaImageBuilder,
                'productFactory' => $this->productFactoryMock,
                'attributeFactory' => $this->attributeFactoryMock,
                'mediaGallery' => $this->mediaGalleryMock,
                'galleryEntryBuilder' => $builder,
            )
        );

        $this->setMock = $this->getMock(
            '\Magento\Eav\Model\Entity\Attribute\Set',
            array('getEntityTypeId', 'load', 'getId', '__wakeup'),
            array(),
            '',
            false
        );

        $this->productMock = $this->getMock(
            'Magento\Catalog\Model\Product',
            array(),
            array(),
            '',
            false
        );
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testTypesForAbsentId()
    {
        $this->setFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->setMock));

        $this->setMock->expects($this->once())
            ->method('load')
            ->with($this->attributeSetId)
            ->will($this->returnSelf());

        $this->setMock->expects($this->once())->method('getId')->will($this->returnValue(null));
        $this->service->types($this->attributeSetId);
    }

    /**
     * @expectedException \Magento\Framework\Exception\InputException
     */
    public function testTypesForWrongEntityType()
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

        $this->service->types($this->attributeSetId);
    }

    public function testTypesPositive()
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
        $this->attributeCollectionMock->expects($this->once())->method('setAttributeSetFilter')
            ->with($this->attributeSetId);
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

        $attributes = $this->service->types($this->attributeSetId);
        $this->assertEquals(1, count($attributes));
        /** @var \Magento\Catalog\Service\V1\Product\Attribute\Media\Data\MediaImage $resultAttribute */
        $resultAttribute = reset($attributes);
        $this->assertEquals('coolLabel', $resultAttribute->getFrontendLabel());
        $this->assertEquals('attribute_code', $resultAttribute->getCode());
        $this->assertEquals(true, $resultAttribute->getIsUserDefined());
        $this->assertEquals('Store View', $resultAttribute->getScope());
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGetListForAbsentSku()
    {
        $sku = 'absentSku';

        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())
            ->method('loadByAttribute')
            ->with('sku', $sku)
            ->will($this->returnValue(false));

        $this->service->getList($sku);
    }

    /**
     * @dataProvider getListProvider
     */
    public function testGetList($gallery, $result, $productDataMap)
    {
        $sku = 'absentSku';
        $productId = 100;
        $productEntityCode = 4;
        $attributes = [
            'image' => 1,
            'small_image'=> 2,
            'thumbnail' => 3
        ];

        $this->productFactoryMock->expects($this->once())
            ->method('create')
            ->will($this->returnValue($this->productMock));

        $this->productMock->expects($this->once())
            ->method('loadByAttribute')
            ->with('sku', $sku)
            ->will($this->returnSelf());

        $this->productMock->expects($this->any())
            ->method('getData')->will($this->returnValueMap($productDataMap));

        $attributeMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Eav\Attribute',
            array(),
            array(),
            '',
            false
        );

        $this->attributeFactoryMock->expects($this->once())->method('create')->will($this->returnValue($attributeMock));
        $this->eavConfigMock->expects($this->once())
            ->method('getEntityType')
            ->with(\Magento\Catalog\Model\Product::ENTITY)
            ->will($this->returnValue($productEntityCode));
        $attributeMock->expects($this->once())->method('loadByCode')->with($productEntityCode, 'media_gallery');
        $this->mediaGalleryMock->expects($this->once())->method('loadGallery')->will($this->returnValue($gallery));
        $this->productMock->expects($this->once())->method('getMediaAttributes')->will($this->returnValue($attributes));

        $serviceOutput = $this->service->getList($sku);
        $this->assertEquals($result, $serviceOutput);
    }

    public function getListProvider()
    {
        $objectHelper = new \Magento\TestFramework\Helper\ObjectManager($this);

        $dataObject = $objectHelper->getObject(
            '\Magento\Catalog\Service\V1\Product\Attribute\Media\Data\GalleryEntryBuilder');
        $dataObject->populateWithArray(
            array(
                'id' => 26,
                'label' => 'Image Alt Text',
                'types' => array('image', 'small_image'),
                'disabled' => 0,
                'position' => 1,
                'file' => '/m/a/magento_image.jpg',
                'store_id' => null,
            )
        );

        $productDataMap = [
            ['image', null, '/m/a/magento_image.jpg'],
            ['small_image', null, '/m/a/magento_image.jpg'],
            ['thumbnail', null, null],
        ];

        return array(
            'empty gallery' => [array(), array(), array()],
            'one image' => [
                array(
                    0 =>
                        array (
                            'value_id' => '26',
                            'file' => '/m/a/magento_image.jpg',
                            'label_default' => 'Image Alt Text',
                            'position_default' => '1',
                            'disabled_default' => '0',
                        ),
                ),
                array($dataObject->create()),
                $productDataMap,
            ],
        );
    }
}