<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Core_Model_Source_Urlrewrite_Types.
 */
class Mage_Core_Model_Source_Urlrewrite_TypesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Mage_Core_Model_Source_Urlrewrite_Types::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new Mage_Core_Model_Source_Urlrewrite_Types();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(
            1 => 'System',
            0 => 'Custom'
        );
        $this->assertEquals($expectedOptions, $options);
    }
}
