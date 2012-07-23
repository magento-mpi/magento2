<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for import product options module
 */
class Mage_ImportExport_Model_Import_Entity_Product_OptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to csv file to import
     */
    const PATH_TO_CSV_FILE = '/_files/product_with_custom_options.csv';

    /**
     * Test store parametes
     *
     * @var array
     */
    protected $_testStores = array(
        array(
            'code' => 'admin',
            'id'   => 0
        )
    );

    /**
     * Tables array to inject into model
     *
     * @var array
     */
    protected $_tables = array(
        'catalog_product_entity'            => 'catalog_product_entity',
        'catalog_product_option'            => 'catalog_product_option',
        'catalog_product_option_title'      => 'catalog_product_option_title',
        'catalog_product_option_type_title' => 'catalog_product_option_type_title',
        'catalog_product_option_type_value' => 'catalog_product_option_type_value',
        'catalog_product_option_type_price' => 'catalog_product_option_type_price',
        'catalog_product_option_price'      => 'catalog_product_option_price',
    );

    /**
     * @var Mage_ImportExport_Model_Import_Entity_Product_Option
     */
    protected $_model;

    /**
     * Array of expected (after import) option titles
     *
     * @var array
     */
    protected $_expectedTitles = array(
        array(
            'option_id' => 3,
            'store_id'  => 0,
            'title'     => 'Test Date and Time Title',
        ),
        array(
            'option_id' => 4,
            'store_id'  => 0,
            'title'     => 'Test Select',
        ),
        array(
            'option_id' => 5,
            'store_id'  => 0,
            'title'     => 'Test Radio',
        ),
        array(
            'option_id' => 1,
            'store_id'  => 0,
            'title'     => 'Test Field Title',
        )
    );

    /**
     * Array of expected (after import) option prices
     *
     * @var array
     */
    protected $_expectedPrices = array(
        2 => array(
            'option_id'  => 1,
            'store_id'   => 0,
            'price'      => 1,
            'price_type' => 'fixed',
        ),
        3 => array(
            'option_id'  => 3,
            'store_id'   => 0,
            'price'      => 2,
            'price_type' => 'fixed',
        ),
    );

    /**
     * Array of expected (after import) option type prices
     *
     * @var array
     */
    protected $_expectedTypePrices = array(
        array(
            'price'          => 3,
            'price_type'     => 'fixed',
            'option_type_id' => 2,
            'store_id'       => 0,
        ),
        array(
            'price'          => 3,
            'price_type'     => 'fixed',
            'option_type_id' => 3,
            'store_id'       => 0,
        ),
        array(
            'price'          => 3,
            'price_type'     => 'fixed',
            'option_type_id' => 4,
            'store_id'       => 0,
        ),
        array(
            'price'          => 3,
            'price_type'     => 'fixed',
            'option_type_id' => 5,
            'store_id'       => 0,
        ),
    );

    /**
     * Array of expected (after import) option type titles
     *
     * @var array
     */
    protected $_expectedTypeTitles = array(
        array(
            'option_type_id' => 2,
            'store_id'       => 0,
            'title'          => 'Option 1',
        ),
        array(
            'option_type_id' => 3,
            'store_id'       => 0,
            'title'          => 'Option 2',
        ),
        array(
            'option_type_id' => 4,
            'store_id'       => 0,
            'title'          => 'Option 1',
        ),
        array(
            'option_type_id' => 5,
            'store_id'       => 0,
            'title'          => 'Option 2',
        ),
    );

    /**
     * Expected updates to catalog_product_entity table after custom options import
     *
     * @var array
     */
    protected $_expectedUpdate = array(
        1 => array(
            'entity_id'        => 1,
            'has_options'      => 1,
            'required_options' => 1,
        ),
    );

    /**
     * Array of expected (after import) options
     *
     * @var array
     */
    protected $_expectedOptions = array(
        array(
            'option_id'      => 1,
            'sku'            => '1-text',
            'max_characters' => '100',
            'file_extension' => NULL,
            'image_size_x'   => 0,
            'image_size_y'   => 0,
            'product_id'     => 1,
            'type'           => 'field',
            'is_require'     => 1,
            'sort_order'     => 0,
        ),
        array(
            'option_id'      => 2,
            'sku'            => '2-date',
            'max_characters' => 0,
            'file_extension' => NULL,
            'image_size_x'   => 0,
            'image_size_y'   => 0,
            'product_id'     => 1,
            'type'           => 'date_time',
            'is_require'     => 1,
            'sort_order'     => 0,
        ),
        array(
            'option_id'      => 3,
            'sku'            => '',
            'max_characters' => 0,
            'file_extension' => NULL,
            'image_size_x'   => 0,
            'image_size_y'   => 0,
            'product_id'     => 1,
            'type'           => 'drop_down',
            'is_require'     => 1,
            'sort_order'     => 0,
        ),
        array(
            'option_id'      => 4,
            'sku'            => '',
            'max_characters' => 0,
            'file_extension' => NULL,
            'image_size_x'   => 0,
            'image_size_y'   => 0,
            'product_id'     => 1,
            'type'           => 'radio',
            'is_require'     => 1,
            'sort_order'     => 0,
        ),
    );

    /**
     * Array of expected (after import) option type values
     *
     * @var array
     */
    protected $_expectedTypeValues = array(
        array(
            'option_type_id' => 2,
            'sort_order'     => 0,
            'sku'            => '3-1-select',
            'option_id'      => 4,
        ),
        array(
            'option_type_id' => 3,
            'sort_order'     => 0,
            'sku'            => '3-2-select',
            'option_id'      => 4,
        ),
        array(
            'option_type_id' => 4,
            'sort_order'     => 0,
            'sku'            => '4-1-radio',
            'option_id'      => 5,
        ),
        array(
            'option_type_id' => 5,
            'sort_order'     => 0,
            'sku'            => '4-2-radio',
            'option_id'      => 5,
        ),
    );

    /**
     * Where which should be generate in case of deleting custom options
     *
     * @var string
     */
    protected $_whereForOption = 'product_id IN (1)';

    /**
     * Where which should be generate in case of deleting custom option types
     *
     * @var string
     */
    protected $_whereForType = 'option_id IN (4, 5)';

    /**
     * Init entity adapter model
     */
    public function setUp()
    {
        $addExpectations = false;
        $deleteBehavior  = false;
        $testName = $this->getName(false);
        if ($testName == 'testImportDataAppendBehavior' || $testName == 'testImportDataDeleteBehavior') {
            $addExpectations = true;
            $deleteBehavior = $this->getName() == 'testImportDataDeleteBehavior' ? true : false;
        }

        $this->_model = new Mage_ImportExport_Model_Import_Entity_Product_Option(
            $this->_getModelDependencies($addExpectations, $deleteBehavior)
        );
    }

    /**
     * Unset entity adapter model
     */
    public function tearDown()
    {
        unset($this->_model);
    }

    /**
     * Create mocks for all $this->_model dependencies
     *
     * @param bool $addExpectations
     * @param bool $deleteBehavior
     * @return array
     */
    protected function _getModelDependencies($addExpectations = false, $deleteBehavior = false)
    {
        $connection = $this->getMock('stdClass', array('delete', 'quoteInto', 'insertMultiple', 'insertOnDuplicate'));
        if ($addExpectations) {
            if ($deleteBehavior) {
                $connection->expects($this->exactly(2))
                    ->method('quoteInto')
                    ->will($this->returnCallback(array($this, 'stubQuoteInto')));
                $connection->expects($this->exactly(2))
                    ->method('delete')
                    ->will($this->returnCallback(array($this, 'verifyDelete')));
            } else {
                $connection->expects($this->once())
                    ->method('insertMultiple')
                    ->will($this->returnCallback(array($this, 'verifyInsertMultiple')));
                $connection->expects($this->exactly(6))
                    ->method('insertOnDuplicate')
                    ->will($this->returnCallback(array($this, 'verifyInsertOnDuplicate')));
            }
        }

        $resourceHelper = $this->getMock('stdClass', array('getNextAutoincrement'));
        if ($addExpectations) {
            $resourceHelper->expects($this->any())
                ->method('getNextAutoincrement')
                ->will($this->returnValue(2));
        }

        $dataHelper = $this->getMock('stdClass', array('__'));
        if ($addExpectations) {
            $dataHelper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));
        }

        $coreResource = $this->getMock('stdClass', array('getTableName'));
        if ($addExpectations) {
            $coreResource->expects($this->any())
                ->method('getTableName')
                ->will($this->returnValue('catalog_product_option_title'));
        }

        $stores = array();
        foreach ($this->_testStores as $store) {
            $stores[$store['code']] = $store['id'];
        }

        $data = array(
            'connection'        => $connection,
            'tables'            => $this->_tables,
            'resource_helper'   => $resourceHelper,
            'data_helper'       => $dataHelper,
            'core_resource'     => $coreResource,
            'is_price_global'   => true,
            'stores'            => $stores,
        );
        $sourceData = $this->_getSourceDataMocks($addExpectations);

        return array_merge($data, $sourceData);
    }

    /**
     * Get source data mocks
     *
     * @param bool $addExpectations
     * @return array
     */
    protected function _getSourceDataMocks($addExpectations)
    {
        $csvData = $this->_loadCsvFile();

        $dataSourceModel = $this->getMock('stdClass', array('getNextBunch'));
        if ($addExpectations) {
            $dataSourceModel->expects($this->at(0))
                ->method('getNextBunch')
                ->will($this->returnValue($csvData['data']));
            $dataSourceModel->expects($this->at(1))
                ->method('getNextBunch')
                ->will($this->returnValue(null));
        }

        $products = array();
        foreach ($csvData['data'] as $rowIndex => $csvDataRow) {
            if (!empty($csvDataRow['sku']) && !array_key_exists($csvDataRow['sku'], $products)) {
                $elementIndex = $rowIndex + 1;
                $products[$csvDataRow['sku']] = array(
                    'sku'        => $csvDataRow['sku'],
                    'id'         => $elementIndex,
                    'entity_id'  => $elementIndex,
                    'product_id' => $elementIndex,
                    'type'       => $csvDataRow[Mage_ImportExport_Model_Import_Entity_Product_Option::COLUMN_TYPE],
                    'title'      => $csvDataRow[Mage_ImportExport_Model_Import_Entity_Product_Option::COLUMN_TITLE],
                );
            }
        }

        $productEntity = $this->getMock('stdClass', array('addMessageTemplate'));
        if ($addExpectations) {
            $productEntity->expects($this->any())
                ->method('addMessageTemplate')
                ->will($this->returnValue(true));
        }

        $productModelMock = $this->getMock('stdClass', array('getProductEntitiesInfo'), array(), '', false);
        $productModelMock->expects($this->any())
            ->method('getProductEntitiesInfo')
            ->will($this->returnValue($products));

        $optionCollection = $this->getMock(
            'Varien_Data_Collection_Db',
            array('reset', 'addProductToFilter', 'getSelect', '_fetchAll')
        );

        $select = $this->getMock('stdClass', array('join', 'where'));
        $select->expects($this->any())
            ->method('join')
            ->will($this->returnValue($select));
        $select->expects($this->any())
            ->method('where')
            ->will($this->returnValue($select));

        $optionCollection->expects($this->any())
            ->method('reset')
            ->will($this->returnValue($optionCollection));
        $optionCollection->expects($this->any())
            ->method('addProductToFilter')
            ->will($this->returnValue($optionCollection));
        $optionCollection->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($select));
        $optionCollection->expects($this->any())
            ->method('_fetchAll')
            ->will($this->returnValue($products));

        $data = array(
            'data_source_model' => $dataSourceModel,
            'product_model'     => $productModelMock,
            'product_entity'    => $productEntity,
            'option_collection' => $optionCollection,
        );
        return $data;
    }

    /**
     * Stub method to emulate adapter quoteInfo() method and get data in needed for test format
     *
     * @param string $text
     * @param array|int|float|string $value
     * @return mixed
     */
    public function stubQuoteInto($text, $value)
    {
        if (is_array($value)) {
            $value = implode(', ', $value);
        }
        return str_replace('?', $value, $text);
    }

    /**
     * Verify data, sent to $this->_connection->delete() method
     *
     * @param string $table
     * @param string $where
     */
    public function verifyDelete($table, $where)
    {
        if ($table == 'catalog_product_option') {
            $this->assertEquals($this->_tables['catalog_product_option'], $table);
            $this->assertEquals($this->_whereForOption, $where);
        } else {
            $this->assertEquals($this->_tables['catalog_product_option_type_value'], $table);
            $this->assertEquals($this->_whereForType, $where);
        }
    }

    /**
     * Verify data, sent to $this->_connection->insertMultiple() method
     *
     * @param string $table
     * @param array $data
     */
    public function verifyInsertMultiple($table, array $data)
    {
        switch ($table) {
            case $this->_tables['catalog_product_option']:
                $this->assertEquals($this->_expectedOptions, $data);
                break;
            case $this->_tables['catalog_product_option_type_value']:
                $this->assertEquals($this->_expectedTypeValues, $data);
                break;
            default:
                break;
        }
    }

    /**
     * Verify data, sent to $this->_connection->insertOnDuplicate() method
     *
     * @param string $table
     * @param array $data
     * @param array $fields
     */
    public function verifyInsertOnDuplicate($table, array $data, array $fields = array())
    {
        switch ($table) {
            case $this->_tables['catalog_product_option_title']:
                $this->assertEquals($this->_expectedTitles, $data);
                $this->assertEquals(array('title'), $fields);
                break;
            case $this->_tables['catalog_product_option_price']:
                $this->assertEquals($this->_expectedPrices, $data);
                $this->assertEquals(array('price', 'price_type'), $fields);
                break;
            case $this->_tables['catalog_product_option_type_price']:
                $this->assertEquals($this->_expectedTypePrices, $data);
                $this->assertEquals(array('price', 'price_type'), $fields);
                break;
            case $this->_tables['catalog_product_option_type_title']:
                $this->assertEquals($this->_expectedTypeTitles, $data);
                $this->assertEquals(array('title'), $fields);
                break;
            case $this->_tables['catalog_product_entity']:
                // there is no point in updated_at data verification which is just current time
                foreach ($data as &$row) {
                    $this->assertArrayHasKey('updated_at', $row);
                    unset($row['updated_at']);
                }
                $this->assertEquals($this->_expectedUpdate, $data);
                $this->assertEquals(array('has_options', 'required_options', 'updated_at'), $fields);
                break;
            default:
                break;
        }
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::getEntityTypeCode
     */
    public function testGetEntityTypeCode()
    {
        $this->assertEquals('product_options', $this->_model->getEntityTypeCode());
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::validateRow
     * @todo   Implement testValidateRow()
     */
    public function testValidateRow()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
          'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::importData
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_importData
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_saveOptions
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_saveTitles
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_savePrices
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_saveSpecificTypeValues
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_saveSpecificTypePrices
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_saveSpecificTypeTitles
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_updateProducts
     */
    public function testImportDataAppendBehavior()
    {
        $this->_model->importData();
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_importData
     * @covers Mage_ImportExport_Model_Import_Entity_Product_Option::_deleteEntities
     */
    public function testImportDataDeleteBehavior()
    {
        $this->_model->setParameters(array('behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE));
        $this->_model->importData();
    }

    protected function _loadCsvFile()
    {
        $data = $this->_csvToArray(file_get_contents(__DIR__ . self::PATH_TO_CSV_FILE));

        return $data;
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
