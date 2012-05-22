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

        $this->_model = new Mage_ImportExport_Model_Export_Entity_Product();

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
}
