<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Layer\Filter\Price;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_base.php
 */
class AlgorithmBaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Layer model
     *
     * @var \Magento\Catalog\Model\Layer
     */
    protected $_layer;

    /**
     * Price filter model
     *
     * @var \Magento\Catalog\Model\Layer\Filter\Price
     */
    protected $_filter;

    /**
     * @var \Magento\Catalog\Model\Resource\Layer\Filter\Price
     */
    protected $priceResource;

    /**
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store catalog/search/engine Magento\CatalogSearch\Model\Resource\Fulltext\Engine
     * @dataProvider pricesSegmentationDataProvider
     */
    public function testPricesSegmentation($categoryId, $intervalsNumber, $intervalItems)
    {
        $layer = Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Layer\Category');
        $priceResource = Bootstrap::getObjectManager()
            ->create('Magento\Catalog\Model\Resource\Layer\Filter\Price', ['layer' => $layer]);
        $interval = Bootstrap::getObjectManager()
            ->create('Magento\CatalogSearch\Model\Price\Interval', ['resource' => $priceResource]);
        $objectManager = $this->getMockBuilder('Magento\Framework\ObjectManager\ObjectManager')
                ->setMethods(['create'])
            ->disableOriginalConstructor()
            ->getMock();
        $objectManager->expects($this->once())->method('create')->willReturn($interval);
        $intervalFactory = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Search\Dynamic\IntervalFactory', ['objectManager' => $objectManager]);
        $model = Bootstrap::getObjectManager()
            ->create('Magento\Framework\Search\Dynamic\Algorithm', ['intervalFactory' => $intervalFactory]);

        $layer->setCurrentCategory($categoryId);
        $collection = $layer->getProductCollection();

        $memoryUsedBefore = memory_get_usage();
        $model->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getSize()
        );
        if (!is_null($intervalsNumber)) {
            $this->assertEquals($intervalsNumber, $model->getIntervalsNumber());
        }

        $items = $model->calculateSeparators();
        $this->assertEquals(array_keys($intervalItems), array_keys($items));

        for ($i = 0; $i < count($intervalItems); ++$i) {
            $this->assertInternalType('array', $items[$i]);
            $this->assertEquals($intervalItems[$i]['from'], $items[$i]['from']);
            $this->assertEquals($intervalItems[$i]['to'], $items[$i]['to']);
            $this->assertEquals($intervalItems[$i]['count'], $items[$i]['count']);
        }

        // Algorythm should use less than 10M
        $this->assertLessThan(10 * 1024 * 1024, memory_get_usage() - $memoryUsedBefore);
    }

    public function pricesSegmentationDataProvider()
    {
        $testCases = include __DIR__ . '/_files/_algorithm_base_data.php';
        $result = array();
        foreach ($testCases as $index => $testCase) {
            $result[] = array(
                $index + 4, //category id
                $testCase[1],
                $testCase[2]
            );
        }

        return $result;
    }
}
