<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model\Layer\Search;

class ItemCollectionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineProviderMock;

    /**
     * @var \Magento\Search\Model\Layer\Search\ItemCollectionProvider
     */
    protected $model;

    protected function setUp()
    {
        $this->engineProviderMock = $this->getMock(
            '\Magento\CatalogSearch\Model\Resource\EngineProvider',
            array(),
            array(),
            '',
            false
        );

        $this->model = new ItemCollectionProvider($this->engineProviderMock);
    }

    /**
     * @covers \Magento\Search\Model\Layer\Search\ItemCollectionProvider::getCollection
     * @covers \Magento\Search\Model\Layer\Search\ItemCollectionProvider::__construct
     */
    public function testGetCollection()
    {
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', array(), array(), '', false);
        $categoryMock->expects($this->once())->method('getStoreId');

        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            array('setStoreId',  'addCategoryFilter', 'setGeneralDefaultQuery'),
            array(),
            '',
            false
        );

        $engineMock = $this->getMock('\Magento\CatalogSearch\Model\Resource\EngineInterface');
        $engineMock->expects($this->once())->method('getResultCollection')->will($this->returnValue($collectionMock));
        $this->engineProviderMock->expects($this->once())->method('get')->will($this->returnValue($engineMock));

        $collectionMock->expects($this->once())->method('setStoreId')->will($this->returnValue($collectionMock));
        $this->assertInstanceOf(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            $this->model->getCollection($categoryMock)
        );
    }
}
