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
 * Test class for version source model Mage_ImportExport_Model_Source_Import_Format_Version
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Import_Format_VersionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Import_Format_Version
     */
    public static $sourceModel;

    /**
     * Instantiate tested source model
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        self::$sourceModel = new Mage_ImportExport_Model_Source_Import_Format_Version();
    }

    /**
     * Is result variable an array
     */
    public function testToArray()
    {
        $basicArray = self::$sourceModel->toArray();
        $this->assertThat($basicArray, $this->isType('array'), 'Result variable should be an array.');
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToVariableArray()
    {
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
