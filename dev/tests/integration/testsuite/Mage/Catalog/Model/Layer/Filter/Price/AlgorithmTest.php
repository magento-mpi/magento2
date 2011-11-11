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

/**
 * Test class for Mage_Catalog_Model_Layer_Filter_Price_Algorithm.
 *
 * @group module:Mage_Catalog
 * @magentoConfigFixture current_store catalog/layered_navigation/price_range_calculation auto
 */
class Mage_Catalog_Model_Layer_Filter_Price_AlgorithmTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Layer_Filter_Price_Algorithm
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Layer_Filter_Price_Algorithm();
    }

    /**
     * @dataProvider pricesSegmentationDataProvider
     */
    public function testPricesSegmentation($prices, $intervalsNumber, $intervalItems)
    {
        $this->_model->setPrices($prices);
        if (!is_null($intervalsNumber)) {
            $this->assertEquals($intervalsNumber, $this->_model->getIntervalsNumber());
        }

        $items = $this->_model->calculateSeparators();
        $this->assertEquals(array_keys($intervalItems), array_keys($items));

        for ($i = 0; $i < count($intervalItems); ++$i) {
            $this->assertInternalType('array', $items[$i]);
            $this->assertEquals($intervalItems[$i]['from'],  $items[$i]['from']);
            $this->assertEquals($intervalItems[$i]['to'],    $items[$i]['to']);
            $this->assertEquals($intervalItems[$i]['count'], $items[$i]['count']);
        }
    }

    public function pricesSegmentationDataProvider()
    {
        return include(__DIR__ . '/_files/_algorithm_data.php');
    }
}
