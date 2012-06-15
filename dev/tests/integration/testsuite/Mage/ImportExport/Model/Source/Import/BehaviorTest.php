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
 * Test class for behaviour source model Mage_ImportExport_Model_Source_Import_Behavior
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Source_Import_BehaviorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Import_Behavior
     */
    public static $sourceModel;

    /**
     * Instantiate tested source model
     *
     * @static
     */
    public static function setUpBeforeClass()
    {
        self::$sourceModel = new Mage_ImportExport_Model_Source_Import_Behavior();
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $optionalArray = self::$sourceModel->toOptionArray();
        $this->assertThat($optionalArray, $this->isType('array'), 'Result variable must be an array.');

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }
    }
}
