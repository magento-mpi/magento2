<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Downloadable
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_SamplesTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples
     */
    protected $_block;

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

    protected function setUp()
    {
        parent::setUp();

        $this->_block = new Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable_Samples();

        $this->_oldDisplayErrors  = ini_get('display_errors');
        $this->_oldErrorLevel = error_reporting();
        $this->_oldIsDeveloperMode = Mage::getIsDeveloperMode();
    }

    protected function tearDown()
    {
        ini_set('display_errors', $this->_oldDisplayErrors);
        error_reporting($this->_oldErrorLevel);
        Mage::setIsDeveloperMode($this->_oldIsDeveloperMode);

        unset($this->_block);

        parent::tearDown();
    }

    /**
     * Test that getConfig method retrieve Varien_Object object
     */
    public function testGetConfig()
    {
        // we have to set strict error reporting mode and enable mage developer mode to convert notice to exception
        error_reporting(E_ALL | E_STRICT);
        ini_set('display_errors', 1);
        Mage::setIsDeveloperMode(true);

        $this->assertInstanceOf('Varien_Object', $this->_block->getConfig());
    }
}
