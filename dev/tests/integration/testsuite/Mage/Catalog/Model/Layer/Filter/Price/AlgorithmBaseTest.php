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
 * Test class for Mage_Catalog_Model_Layer_Filter_Price.
 *
 * @group module:Mage_Catalog
 * @magentoDataFixture Mage/Catalog/Model/Layer/Filter/Price/_files/products_base.php
 */
class Mage_Catalog_Model_Layer_Filter_Price_AlgorithmBaseTest extends PHPUnit_Framework_TestCase
{
    /**
     * Algorithm model
     *
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
    public function testPricesSegmentation($categoryId, $intervalsNumber, $intervalItems)
    {
        ini_set('memory_limit', '128M');
        $layer = new Mage_Catalog_Model_Layer();
        $layer->setCurrentCategory($categoryId);
        $filter = new Mage_Catalog_Model_Layer_Filter_Price();
        $filter->setLayer($layer)->setAttributeModel(new Varien_Object(array('attribute_code' => 'price')));
        $collection = $layer->getProductCollection();
        $this->_model->setPricesModel($filter)->setStatistics(
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
        ini_restore('memory_limit');
    }

    public function pricesSegmentationDataProvider()
    {
        $testCases = include(dirname(__FILE__) . '/_files/_algorithm_base_data.php');
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
