<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->model = new CacheStateTags();
    }

    public function testGetListComposesListWithCacheTagsForGivenCategory()
    {
        $categoryId = 1;
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue($categoryId));
        $additionalTags = array(
            'CUSTOM_CACHE_TAG',
        );
        $expectedResult = array(
            'CUSTOM_CACHE_TAG',
            'catalog_category1',
            'catalog_category1_SEARCH',
        );
        $this->assertEquals($expectedResult, $this->model->getList($categoryMock, $additionalTags));
    }
}
