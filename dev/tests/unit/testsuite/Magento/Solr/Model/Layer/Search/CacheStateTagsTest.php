<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Layer\Search;

class CacheStateTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Layer\Search\CacheStateTags
     */
    protected $model;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->model = new CacheStateTags();
    }

    public function testGetListComposesListWithCacheTagsForGivenCategory()
    {
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $expectedResult = [
            'CUSTOM_CACHE_TAG',
            'catalog_category1',
            'catalog_category1_SEARCH',
            'SEARCH_QUERY',
        ];
        $this->assertEquals($expectedResult, $this->model->getList($categoryMock, ['CUSTOM_CACHE_TAG']));
    }
}
