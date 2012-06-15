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
 * Test class for version source model Mage_ImportExport_Model_Source_Format_Version
 */
class Mage_ImportExport_Model_Source_Format_VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Format_Version
     */
    public static $sourceModel;

    /**
     * Helper registry key
     *
     * @var string
     */
    protected static $_helperKey = '_helper/Mage_ImportExport_Helper_Data';

    /**
     * Helper property
     *
     * @var Mage_ImportExport_Helper_Data|PHPUnit_Framework_MockObject_MockObject
     */
    protected static $_helper;

    /**
     * Mock helper
     *
     * @static
     *
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$sourceModel = new Mage_ImportExport_Model_Source_Format_Version();
    }

    /**
     * Unregister helper
     *
     * @static
     *
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();
        Mage::unregister(self::$_helperKey);
        self::$_helper = null;
        self::$sourceModel = null;
    }

    /**
     * Helper initialization
     *
     * @return Mage_ImportExport_Helper_Data
     */
    protected function _initHelper()
    {
        if (!self::$_helper) {
            self::$_helper = $this->getMock(
                'Mage_ImportExport_Helper_Data',
                array('__')
            );
            self::$_helper->expects($this->any())
                ->method('__')
                ->will($this->returnArgument(0));

            Mage::unregister(self::$_helperKey);
            Mage::register(self::$_helperKey, self::$_helper);
        }
        return self::$_helper;
    }

    /**
     * Is result variable an array
     */
    public function testToArray()
    {
        $this->_initHelper();

        $basicArray = self::$sourceModel->toArray();
        $this->assertThat($basicArray, $this->isType('array'), 'Result variable should be an array.');
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $this->_initHelper();

        $optionalArray = self::$sourceModel->toOptionArray();
        $this->assertThat($optionalArray, $this->isType('array'), 'Result variable should be an array.');

        $basicArray = self::$sourceModel->toArray();
        // count + 1 = all values + header
        $this->assertCount(count($basicArray) + 1, $optionalArray, 'Incorrect number of elements in optional array.');

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }
    }
}
