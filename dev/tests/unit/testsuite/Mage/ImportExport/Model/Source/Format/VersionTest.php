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
class Mage_ImportExport_Model_Source_Format_VersionTest extends Mage_ImportExport_Model_Source_TestCaseAbstract
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Format_Version
     */
    public static $sourceModel;

    /**
     * Init source model
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();
        self::$sourceModel = new Mage_ImportExport_Model_Source_Format_Version();
    }

    /**
     * Is result variable an array
     */
    public function testToArray()
    {
        $this->_initHelper();

        $basicArray = self::$sourceModel->toArray();
        $this->assertInternalType('array', $basicArray, 'Result variable must be an array.');
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $this->_initHelper();

        $optionalArray = self::$sourceModel->toOptionArray();
        $this->assertInternalType('array', $optionalArray, 'Result variable must be an array.');

        $basicArray = self::$sourceModel->toArray();
        // count + 1 = all values + header
        $this->assertCount(count($basicArray) + 1, $optionalArray, 'Incorrect number of elements in optional array.');

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }
    }
}
