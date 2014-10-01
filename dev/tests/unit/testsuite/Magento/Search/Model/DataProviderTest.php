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
        /** @var \Magento\Search\Model\SearchDataProvider $recommendations */
        $dataProvider = $this->objectManager->getObject('\Magento\Search\Model\AdditionalInfoDataProvider');
        $this->assertEquals([], $dataProvider->getSearchResult('some text'));
    }

    public function testIsCountResultsEnabled()
    {
        /** @var \Magento\Search\Model\SearchDataProvider $dataProvider */
        $dataProvider = $this->objectManager->getObject('\Magento\Search\Model\AdditionalInfoDataProvider');
        $this->assertFalse($dataProvider->isCountResultsEnabled());
    }
}
