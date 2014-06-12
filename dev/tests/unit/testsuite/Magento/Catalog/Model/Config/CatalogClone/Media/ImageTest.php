<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\CatalogClone\Media;

use Magento\Catalog\Model\Product;
use Magento\TestFramework\Helper\ObjectManager;

class ImageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Config\CatalogClone\Media\Image
     */
    private $model;

    /**
     * @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockedEavConfig;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockedAttributeCollectionFactory;

    /**
     * @var \Magento\Catalog\Model\Resource\Product\Attribute\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockedAttributeCollection;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute|\PHPUnit_Framework_MockObject_MockObject
     */
    private $mockedAttribute;

    protected function setUp()
    {
        $this->mockedEavConfig = $this->getMockBuilder('Magento\Eav\Model\Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockedAttributeCollection = $this->getMockBuilder(
            '\Magento\Catalog\Model\Resource\Product\Attribute\Collection'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->mockedAttributeCollectionFactory = $this->getMockBuilder(
            'Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory'
        )
            ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $this->mockedAttributeCollectionFactory->expects($this->any())->method('create')->will(
            $this->returnValue($this->mockedAttributeCollection)
        );

        $this->mockedAttribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute')
            ->disableOriginalConstructor()
            ->getMock();

        $helper = new ObjectManager($this);
        $this->model = $helper->getObject(
            'Magento\Catalog\Model\Config\CatalogClone\Media\Image',
            [
                'eavConfig' => $this->mockedEavConfig,
                'attributeCollectionFactory' => $this->mockedAttributeCollectionFactory
            ]
        );
    }

    public function testGetPrefixes()
    {
        $entityTypeId = 3;
        /** @var \Magento\Eav\Model\Entity\Type|\PHPUnit_Framework_MockObject_MockObject $mockedEntityType */
        $mockedEntityType = $this->getMockBuilder('Magento\Eav\Model\Entity\Type')
            ->disableOriginalConstructor()
            ->getMock();
        $mockedEntityType->expects($this->once())->method('getId')->will($this->returnValue($entityTypeId));

        /** @var \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend|\PHPUnit_Framework_MockObject_MockObject $mockedFrontend */
        $mockedFrontend = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend')
            ->setMethods(['getLabel'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $mockedFrontend->expects($this->once())->method('getLabel')->will($this->returnValue('testLabel'));

        $this->mockedAttributeCollection->expects($this->once())->method('setEntityTypeFilter')->with(
            $this->equalTo($entityTypeId)
        );
        $this->mockedAttributeCollection->expects($this->once())->method('setFrontendInputTypeFilter')->with(
            $this->equalTo('media_image')
        );

        $this->mockedAttribute->expects($this->once())->method('getAttributeCode')->will(
            $this->returnValue('attributeCode')
        );
        $this->mockedAttribute->expects($this->once())->method('getFrontend')->will(
            $this->returnValue($mockedFrontend)
        );

        $this->mockedAttributeCollection->expects($this->any())->method('getIterator')->will(
            $this->returnValue(new \ArrayIterator([$this->mockedAttribute]))
        );

        $this->mockedEavConfig->expects($this->any())->method('getEntityType')->with(
            $this->equalTo(Product::ENTITY)
        )->will($this->returnValue($mockedEntityType));

        $this->assertEquals([['field' => 'attributeCode_', 'label' => 'testLabel']], $this->model->getPrefixes());
    }
}
