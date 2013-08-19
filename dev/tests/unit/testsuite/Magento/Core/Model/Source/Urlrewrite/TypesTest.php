<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Core_Model_Source_Urlrewrite_Types.
 */
class Magento_Core_Model_Source_Urlrewrite_TypesTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Core_Model_Source_Urlrewrite_Types::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new Magento_Core_Model_Source_Urlrewrite_Types();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(
            1 => 'System',
            0 => 'Custom'
        );
        $this->assertEquals($expectedOptions, $options);
    }
}
