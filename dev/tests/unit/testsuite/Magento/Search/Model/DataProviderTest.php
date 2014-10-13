<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Search\Model;

use Magento\TestFramework\Helper\ObjectManager;

class DataProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var ObjectManager
     */
    private $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
    }

    public function testGetRecommendations()
    {
        /** @var \Magento\Search\Model\QueryInterface|\PHPUnit_Framework_MockObject_MockObject $searchQuery */
        $searchQuery = $this->getMockBuilder('\Magento\Search\Model\QueryInterface')->getMockForAbstractClass();
        /** @var \Magento\Search\Model\SearchDataProvider $dataProvider */
        $dataProvider = $this->objectManager->getObject('\Magento\Search\Model\SearchDataProvider');
        $this->assertEquals([], $dataProvider->getSearchData($searchQuery));
    }

    public function testIsCountResultsEnabled()
    {
        /** @var \Magento\Search\Model\SearchDataProvider $dataProvider */
        $dataProvider = $this->objectManager->getObject('\Magento\Search\Model\SearchDataProvider');
        $this->assertFalse($dataProvider->isCountResultsEnabled());
    }
}
