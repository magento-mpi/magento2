<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product\Media;

use \Magento\Catalog\Model\Product;

class AttributeManagementTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AttributeManagement
     */
    private $model;

    /**
     * @var int
     */
    private $storeId;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $factoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $storeManagerMock;

    protected function setUp()
    {
        $this->factoryMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\CollectionFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->storeId = 1;
        $this->storeManagerMock = $this->getMock('\Magento\Store\Model\StoreManagerInterface');
        $storeMock = $this->getMock('\Magento\Store\Model\Store', array(), array(), '', false);
        $storeMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($this->storeId));
        $this->storeManagerMock->expects($this->any())
            ->method('getStore')
            ->with(null)
            ->will($this->returnValue($storeMock));
        $this->model = new AttributeManagement(
            $this->factoryMock,
            $this->storeManagerMock
        );
    }

    public function testGetList()
    {
        $attributeSetName = 'Default Attribute Set';
        $expectedResult = array(
            $this->getMock('\Magento\Catalog\Api\Data\ProductAttributeInterface'),
        );
        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Attribute\Collection',
            array(),
            array(),
            '',
            false
        );
        $collectionMock->expects($this->once())
            ->method('setAttributeSetFilterBySetName')
            ->with($attributeSetName, Product::ENTITY);
        $collectionMock->expects($this->once())
            ->method('setFrontendInputTypeFilter')
            ->with('media_image');
        $collectionMock->expects($this->once())
            ->method('addStoreLabel')
            ->with($this->storeId);
        $collectionMock->expects($this->once())
            ->method('getItems')
            ->will($this->returnValue($expectedResult));
        $this->factoryMock->expects($this->once())->method('create')->will($this->returnValue($collectionMock));

        $this->assertEquals($expectedResult, $this->model->getList($attributeSetName));
    }
}
