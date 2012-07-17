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
 */
class Mage_ImportExport_Model_Source_Import_BehaviorTest extends Mage_ImportExport_Model_Source_TestCaseAbstract
{
    /**
     * Tested source model
     *
     * @var Mage_ImportExport_Model_Source_Import_Behavior
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
        self::$sourceModel = new Mage_ImportExport_Model_Source_Import_Behavior();
    }

    /**
     * Is result variable an correct optional array
     */
    public function testToOptionArray()
    {
        $this->_initHelper();

        $optionalArray = self::$sourceModel->toOptionArray();
        $this->assertInternalType('array', $optionalArray, 'Result variable must be an array.');

        foreach ($optionalArray as $option) {
            $this->assertArrayHasKey('label', $option, 'Option must have label property.');
            $this->assertArrayHasKey('value', $option, 'Option must have value property.');
        }
    }
}
