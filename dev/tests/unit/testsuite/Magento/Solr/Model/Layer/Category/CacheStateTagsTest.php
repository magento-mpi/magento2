<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Solr\Model\Layer\Category;

class CacheStateTagsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Solr\Model\Layer\Category\CacheStateTags
     */
    protected $model;

    protected function setUp()
    {
        $this->markTestSkipped('Solr module disabled');
        $this->model = new CacheStateTags();
    }

    public function testGetListComposesListWithCacheTagsForGivenCategory()
    {
        $categoryId = 1;
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue($categoryId));
        $additionalTags = [
            'CUSTOM_CACHE_TAG',
        ];
        $expectedResult = [
            'CUSTOM_CACHE_TAG',
            'catalog_category1',
            'catalog_category1_SEARCH',
        ];
        $this->assertEquals($expectedResult, $this->model->getList($categoryMock, $additionalTags));
    }
}
