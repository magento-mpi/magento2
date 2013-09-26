<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_ImportExport_Model_Export_Entity_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Export_Entity_Product
     */
    protected $_model;

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

        $this->_model = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Entity_Product');
    }

    /**
     * @magentoDataFixture Magento/ImportExport/_files/product.php
     */
    public function testExport()
    {
        $this->_model->setWriter(Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_ImportExport_Model_Export_Adapter_Csv'));
        $this->assertNotEmpty($this->_model->export());
    }

    /**
     * Verify that all stock item attribute values are exported (aren't equal to empty string)
     *
     * @covers Magento_ImportExport_Model_Export_Entity_Product::export
     * @magentoDataFixture Magento/ImportExport/_files/product.php
     */
    public function testExportStockItemAttributesAreFilled()
    {
        $writerMock = $this->getMockForAbstractClass(
            'Magento_ImportExport_Model_Export_Adapter_Abstract',
            array(),
            '',
            true,
            true,
            true,
            array('setHeaderCols', 'writeRow')
        );

        $writerMock->expects($this->any())
            ->method('setHeaderCols')
            ->will($this->returnCallback(array($this, 'verifyHeaderColumns')));

        $writerMock->expects($this->any())
            ->method('writeRow')
            ->will($this->returnCallback(array($this, 'verifyRow')));

        $this->_model->setWriter($writerMock)
            ->export();
    }

    /**
     * Verify header columns (that stock item attributes column headers are present)
     *
     * @param array $headerColumns
     */
    public function verifyHeaderColumns(array $headerColumns)
    {
        foreach (self::$stockItemAttributes as $stockItemAttribute) {
            $this->assertContains($stockItemAttribute, $headerColumns,
                "Stock item attribute {$stockItemAttribute} is absent among header columns"
            );
        }
    }

    /**
     * Verify row data (stock item attribute values)
     *
     * @param array $rowData
     */
    public function verifyRow(array $rowData)
    {
        foreach (self::$stockItemAttributes as $stockItemAttribute) {
            $this->assertNotSame('', $rowData[$stockItemAttribute],
                "Stock item attribute {$stockItemAttribute} value is empty string"
            );
        }
    }
}
