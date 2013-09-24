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

namespace Magento\ImportExport\Model\Export\Entity;

class ProductTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\ImportExport\Model\Export\Entity\Product
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

        $this->_model = \Mage::getModel('Magento\ImportExport\Model\Export\Entity\Product');
    }

    /**
     * @magentoDataFixture Magento/ImportExport/_files/product.php
     */
    public function testExport()
    {
        $this->_model->setWriter(\Mage::getModel('Magento\ImportExport\Model\Export\Adapter\Csv'));
        $this->assertNotEmpty($this->_model->export());
    }

    /**
     * Verify that all stock item attribute values are exported (aren't equal to empty string)
     *
     * @covers \Magento\ImportExport\Model\Export\Entity\Product::export
     * @magentoDataFixture Magento/ImportExport/_files/product.php
     */
    public function testExportStockItemAttributesAreFilled()
    {
        $writerMock = $this->getMockForAbstractClass(
            'Magento\ImportExport\Model\Export\Adapter\AbstractAdapter',
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
