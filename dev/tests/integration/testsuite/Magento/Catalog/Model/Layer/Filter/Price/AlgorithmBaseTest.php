<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter\Price;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_base.php
 */
class AlgorithmBaseTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Algorithm model
     *
     * @var \Magento\Catalog\Model\Layer\Filter\Price\Algorithm
     */
    protected $_model;

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

    protected function setUp()
    {
        $this->_model = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Price\Algorithm');
        $this->_layer = \Mage::getModel('Magento\Catalog\Model\Layer');
        $this->_filter = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Price');
        $this->_filter
            ->setLayer($this->_layer)
            ->setAttributeModel(new \Magento\Object(array('attribute_code' => 'price')));
    }

    /**
     * @dataProvider pricesSegmentationDataProvider
     */
    public function testPricesSegmentation($categoryId, $intervalsNumber, $intervalItems)
    {
        $this->_layer->setCurrentCategory($categoryId);
        $collection = $this->_layer->getProductCollection();

        $memoryUsedBefore = memory_get_usage();
        $this->_model->setPricesModel($this->_filter)->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getSize()
        );
        if (!is_null($intervalsNumber)) {
            $this->assertEquals($intervalsNumber, $this->_model->getIntervalsNumber());
        }

        $items = $this->_model->calculateSeparators();
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
        $testCases = include(__DIR__ . '/_files/_algorithm_base_data.php');
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
