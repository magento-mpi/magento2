<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_ImportExport_Model_Import_Entity_Product
 */
class Mage_ImportExport_Model_Import_Entity_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Import_Entity_Product
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_ImportExport_Model_Import_Entity_Product();
    }

    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param $behavior
     *
     * @magentoDataFixture Mage/Catalog/_files/product_simple.php
     * @dataProvider getBehaviorDataProvider
     * @covers Mage_ImportExport_Model_Import_Entity_Product::_saveCustomOptions
     */
    public function testSaveCustomOptions($behavior)
    {
        /** @var $testProduct Mage_Catalog_Model_Product */
        $testProduct = Mage::registry('_fixture/Mage_Catalog_Product_Simple');

        $pathToFile = __DIR__ . '/_files/product_with_custom_options.csv';
        $source = new Mage_ImportExport_Model_Import_Adapter_Csv($pathToFile);
        $this->_model->setSource($source)
            ->setParameters(array('behavior' => $behavior))
            ->isDataValid();
        $this->_model->importData();

        $product = new Mage_Catalog_Model_Product();
        $product->load($testProduct->getId());
        $options = $product->getProductOptionsCollection();

        $productData = $this->_csvToArray(file_get_contents($pathToFile));
        $expectedOptions = array();
        foreach ($productData['data'] as $data) {
            if (!empty($data['_custom_option_type']) && !empty($data['_custom_option_title'])) {
                $expectedOptions[] = $data['_custom_option_type'] . '|' . $data['_custom_option_title'];
            }
        }

        $actualOptions = array();
        /** @var $option Mage_Catalog_Model_Product_Option */
        foreach ($options->getItems() as $option) {
            $actualOptions[] = $option->getType() . '|' . $option->getTitle();
        }

        if ($behavior == Mage_ImportExport_Model_Import::BEHAVIOR_APPEND) {
            $actualOptionsCount = $options->count() / 2;
        } else {
            $actualOptionsCount = $options->count();
        }

        $this->assertCount($actualOptionsCount, $expectedOptions);

        $this->assertCount(
            count($expectedOptions),
            array_intersect($expectedOptions, $actualOptions),
            'Custom options were not exported successfully'
        );
    }

    /**
     * Data provider for test 'testSaveCustomOptions'
     *
     * @return array
     */
    public function getBehaviorDataProvider()
    {
        return array(
            'Append behavior' => array(
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND
            ),
            'Replace behavior' => array(
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_REPLACE
            )
        );
    }

    /**
     * Export CSV string to array
     *
     * @param string $content
     * @param mixed $entityId
     * @return array
     */
    protected function _csvToArray($content, $entityId = null)
    {
        $data = array(
            'header' => array(),
            'data'   => array()
        );

        $lines = str_getcsv($content, "\n");
        foreach ($lines as $index => $line) {
            if ($index == 0) {
                $data['header'] = str_getcsv($line);
            } else {
                $row = array_combine($data['header'], str_getcsv($line));
                if (!is_null($entityId) && !empty($row[$entityId])) {
                    $data['data'][$row[$entityId]] = $row;
                } else {
                    $data['data'][] = $row;
                }
            }
        }
        return $data;
    }
}
