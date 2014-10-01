<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Block;

use Magento\TestFramework\Helper\ObjectManager;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class SearchDataTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\Search\Model\QueryFactoryInterface|MockObject
     */
    private $queryFactory;

    /**
     * @var \Magento\CatalogSearch\Model\Query|MockObject
     */
    private $searchQuery;
    /**
     * @var \Magento\Search\Model\SearchDataProviderInterface|MockObject
     */
    private $dataProvider;

    /**
     * @var \Magento\Search\Block\SearchData
     */
    private $block;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->dataProvider = $this->getMockBuilder('\Magento\Search\Model\SearchDataProviderInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getSearchData', 'isCountResultsEnabled'])
            ->getMockForAbstractClass();

        $this->searchQuery = $this->getMockBuilder('\Magento\Search\Model\QueryInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryText'])
            ->getMockForAbstractClass();
        $this->queryFactory = $this->getMockBuilder('\Magento\Search\Model\QueryFactoryInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery'])
            ->getMockForAbstractClass();
        $this->queryFactory->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->searchQuery));
        $this->block = $objectManager->getObject(
            '\Magento\Search\Block\SearchData',
            [
                'searchDataProvider' => $this->dataProvider,
                'queryFactory' => $this->queryFactory,
            ]
        );
    }

    public function testGetSuggestions()
    {
        $value = [1, 2, 3, 100500,];

        $this->dataProvider->expects($this->once())
            ->method('getSearchData')
            ->with($this->searchQuery)
            ->will($this->returnValue($value));
        $actualValue = $this->block->getSearchData();
        $this->assertEquals($value, $actualValue);
    }

    public function testGetLink()
    {
        $searchQuery = 'Some test search query';
        $expectedResult = '?q=Some+test+search+query';
        $actualResult = $this->block->getLink($searchQuery);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testIsCountResultsEnabled()
    {
        $value = 'qwertyasdfzxcv';
        $this->dataProvider->expects($this->once())
            ->method('isCountResultsEnabled')
            ->will($this->returnValue($value));
        $this->assertEquals($value, $this->block->isCountResultsEnabled());
    }
}
