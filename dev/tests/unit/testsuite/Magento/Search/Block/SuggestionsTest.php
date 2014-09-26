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

class SuggestionsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var \Magento\CatalogSearch\Model\Query|MockObject
     */
    private $catalogSearchQuery;

    /**
     * @var \Magento\CatalogSearch\Helper\Data|MockObject
     */
    private $catalogSearchData;

    /**
     * @var \Magento\Search\Model\SuggestionsInterface|MockObject
     */
    private $suggestions;

    /**
     * @var \Magento\Search\Block\Suggestions
     */
    private $block;

    protected function setUp()
    {
        $objectManager = new ObjectManager($this);

        $this->suggestions = $this->getMockBuilder('\Magento\Search\Model\SuggestionsInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getSuggestions', 'isCountResultsEnabled'])
            ->getMockForAbstractClass();

        $this->catalogSearchData = $this->getMockBuilder('\Magento\CatalogSearch\Helper\Data')
            ->disableOriginalConstructor()
            ->setMethods(['getQuery'])
            ->getMock();

        $this->catalogSearchQuery = $this->getMockBuilder('\Magento\CatalogSearch\Model\Query')
            ->disableOriginalConstructor()
            ->setMethods(['getQueryText'])
            ->getMock();

        $this->block = $objectManager->getObject(
            '\Magento\Search\Block\Suggestions',
            [
                'suggestions' => $this->suggestions,
                'catalogSearchData' => $this->catalogSearchData,
            ]
        );
    }

    public function testGetSuggestions()
    {
        $searchQuery = 'Some test search query';
        $value = [1, 2, 3, 100500,];
        $this->catalogSearchData->expects($this->once())
            ->method('getQuery')
            ->will($this->returnValue($this->catalogSearchQuery));
        $this->catalogSearchQuery->expects($this->once())
            ->method('getQueryText')
            ->will($this->returnValue($searchQuery));

        $this->suggestions->expects($this->once())
            ->method('getSuggestions')
            ->with($searchQuery)
            ->will($this->returnValue($value));
        $actualValue = $this->block->getSuggestions();
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
        $this->suggestions->expects($this->once())
            ->method('isCountResultsEnabled')
            ->will($this->returnValue($value));
        $this->assertEquals($value, $this->block->isCountResultsEnabled());
    }
}
