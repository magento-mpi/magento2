<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model\Resource\Report\Product\Viewed;

/**
 * @magentoAppArea adminhtml
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Reports\Model\Resource\Report\Product\Viewed\Collection
     */
    private $_collection;

    protected function setUp()
    {
        $this->_collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Reports\Model\Resource\Report\Product\Viewed\Collection'
        );
        $this->_collection->setPeriod('day')->setDateRange(null, null)->addStoreFilter(array(1));
    }

    /**
     * @magentoDataFixture Magento/Reports/_files/viewed_products.php
     */
    public function testGetItems()
    {
        $expectedResult = array(1 => 3, 2 => 1, 21 => 2);
        $actualResult = array();
        /** @var \Magento\Reports\Model\Item $reportItem */
        foreach ($this->_collection->getItems() as $reportItem) {
            $actualResult[$reportItem->getData('product_id')] = $reportItem->getData('views_num');
        }
        $this->assertEquals($expectedResult, $actualResult);
    }
}
