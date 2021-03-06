<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Solr\Model\Layer\Search;

class ItemCollectionProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $engineProviderMock;

    /**
     * @var \Magento\Solr\Model\Layer\Search\ItemCollectionProvider
     */
    protected $model;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->engineProviderMock = $this->getMock(
            '\Magento\CatalogSearch\Model\Resource\EngineProvider',
            [],
            [],
            '',
            false
        );

        $this->model = new ItemCollectionProvider($this->engineProviderMock);
    }

    /**
     * @covers \Magento\Solr\Model\Layer\Search\ItemCollectionProvider::getCollection
     * @covers \Magento\Solr\Model\Layer\Search\ItemCollectionProvider::__construct
     */
    public function testGetCollection()
    {
        $categoryMock = $this->getMock('\Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects($this->once())->method('getStoreId');

        $collectionMock = $this->getMock(
            '\Magento\Catalog\Model\Resource\Product\Collection',
            ['setStoreId',  'addCategoryFilter', 'setGeneralDefaultQuery'],
            [],
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
