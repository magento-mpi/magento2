<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->model = new CacheStateTags();
    }

    public function testGetListComposesListWithCacheTagsForGivenCategory()
    {
        $categoryMock = $this->getMock('Magento\Catalog\Model\Category', array(), array(), '', false);
        $categoryMock->expects($this->any())->method('getId')->will($this->returnValue(1));
        $expectedResult = array(
            'CUSTOM_CACHE_TAG',
            'catalog_category1',
            'catalog_category1_SEARCH',
            'SEARCH_QUERY',
        );
        $this->assertEquals($expectedResult, $this->model->getList($categoryMock, array('CUSTOM_CACHE_TAG')));
    }
}
