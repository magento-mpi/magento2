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

class Mage_ImportExport_Model_Export_Entity_ProductTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_Product
     */
    protected $_object;

    /**
     * Store old display_errors ini option value here
     *
     * @var int
     */
    protected $_oldDisplayErrorsValue;

    /**
     * Store old error_reporting ini option value here
     *
     * @var int
     */
    protected $_oldErrorReportingLever;

    /**
     * Store old isDeveloperMode value here
     *
     * @var boolean
     */
    protected $_oldIsDeveloperMode;

    protected function setUp()
    {
        parent::setUp();

        $this->_object = new Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_Product();

        $this->_oldDisplayErrorsValue  = ini_get('display_errors');
        $this->_oldErrorReportingLever = error_reporting();
        $this->_oldIsDeveloperMode = Mage::getIsDeveloperMode();
    }

    protected function tearDown()
    {
        ini_set('display_errors', $this->_oldDisplayErrorsValue);
        error_reporting($this->_oldErrorReportingLever);
        Mage::setIsDeveloperMode($this->_oldIsDeveloperMode);

        parent::tearDown();
    }

    /**
     * Test that there is no notice in _updateDataWithCategoryColumns()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_Product::_updateDataWithCategoryColumns
     */
    public function testUpdateDataWithCategoryColumnsNoCategoriesAssigned()
    {
        $dataRow = array();
        $productId = 1;
        $rowCategories = array($productId => array());

        // we have to set strict error reporting mode and enable mage developer mode to convert notice to exception
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);
        Mage::setIsDeveloperMode(true);

        $this->assertTrue($this->_object->updateDataWithCategoryColumns($dataRow, $rowCategories, $productId));
    }
}

/**
 * We had to create this stub class because _updateDataWithCategoryColumns() parameters are passed by reference -
 * we can't use ReflectionMethod::setAccessible() and then ReflectionMethod::invokeArgs() to call it from test.
 */
class Stub_UnitTest_Mage_ImportExport_Model_Export_Entity_Product extends Mage_ImportExport_Model_Export_Entity_Product
{
    /**
     * Disable parent constructor
     */
    public function __construct() {}

    /**
     * Update data row with information about categories. Return true, if data row was updated
     *
     * @param array $dataRow
     * @param array $rowCategories
     * @param int $productId
     * @return bool
     */
    public function updateDataWithCategoryColumns(&$dataRow, &$rowCategories, $productId)
    {
        return $this->_updateDataWithCategoryColumns($dataRow, $rowCategories, $productId);
    }
}
