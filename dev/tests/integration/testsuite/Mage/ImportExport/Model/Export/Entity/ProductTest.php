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
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Export_Entity_Product
     */
    protected $_model;

    /**
     * Store old display_errors ini option value here
     *
     * @var int
     */
    protected $_oldDisplayErrors;

    /**
     * Store old error_reporting ini option value here
     *
     * @var int
     */
    protected $_oldErrorLevel;

    /**
     * Store old isDeveloperMode value here
     *
     * @var boolean
     */
    protected $_oldIsDeveloperMode;

    /**
     * Stock item attributes which must be exported
     *
     * @var array
     */
    public static $stockItemAttributes = array(
        'qty',
        'min_qty',
        'use_config_min_qty',
        'is_qty_decimal',
        'backorders',
        'use_config_backorders',
        'min_sale_qty',
        'use_config_min_sale_qty',
        'max_sale_qty',
        'use_config_max_sale_qty',
        'is_in_stock',
        'notify_stock_qty',
        'use_config_notify_stock_qty',
        'manage_stock',
        'use_config_manage_stock',
        'use_config_qty_increments',
        'qty_increments',
        'use_config_enable_qty_inc',
        'enable_qty_increments',
        'is_decimal_divided',
    );

    protected function setUp()
    {
        parent::setUp();

        $this->_model = new Mage_ImportExport_Model_Export_Entity_Product();

        $this->_oldDisplayErrors  = ini_get('display_errors');
        $this->_oldErrorLevel = error_reporting();
        $this->_oldIsDeveloperMode = Mage::getIsDeveloperMode();
    }

    protected function tearDown()
    {
        ini_set('display_errors', $this->_oldDisplayErrors);
        error_reporting($this->_oldErrorLevel);
        Mage::setIsDeveloperMode($this->_oldIsDeveloperMode);

        parent::tearDown();
    }

    /**
     * Test that there is no notice in _updateDataWithCategoryColumns()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_Product::_updateDataWithCategoryColumns
     *
     * @magentoDataFixture Mage/ImportExport/_files/product.php
     */
    public function testExport()
    {
        // we have to set strict error reporting mode and enable mage developer mode to convert notice to exception
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);
        Mage::setIsDeveloperMode(true);

        $this->_model->setWriter(new Mage_ImportExport_Model_Export_Adapter_Csv());
        $this->assertNotEmpty($this->_model->export());
    }

    /**
     * Verify that all stock item attribute values are exported (aren't equal to empty string)
     *
     * @covers Mage_ImportExport_Model_Export_Entity_Product::export()
     *
     * @magentoDataFixture Mage/ImportExport/_files/product.php
     */
    public function testExportStockItemAttributesAreFilled()
    {
        $this->_model->setWriter(new Mage_ImportExport_Model_Export_Adapter_IntegrationTest())
            ->export();
    }
}

/**
 * We create our test writer class to be sure that data sent to writer
 * from Mage_ImportExport_Model_Export_Entity_Product::export() is correct and not affected by writer
 */
class Mage_ImportExport_Model_Export_Adapter_IntegrationTest extends
    Mage_ImportExport_Model_Export_Adapter_Abstract
{
    /**
     * Verify row data (stock item attribute values)
     *
     * @param array $rowData
     * @return Mage_ImportExport_Model_Export_Adapter_IntegrationTest
     */
    public function writeRow(array $rowData)
    {
        foreach (Mage_ImportExport_Model_Export_Entity_ProductTest::$stockItemAttributes as $stockItemAttribute) {
            PHPUnit_Framework_TestCase::assertNotSame('', $rowData[$stockItemAttribute],
                "Stock item attribute {$stockItemAttribute} value is empty string"
            );
        }
    }
}
