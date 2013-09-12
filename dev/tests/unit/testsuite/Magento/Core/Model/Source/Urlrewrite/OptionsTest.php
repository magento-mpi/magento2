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
 * Test class for Magento_Core_Model_Source_Urlrewrite_OptionsTest.
 */
class Magento_Core_Model_Source_Urlrewrite_OptionsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers Magento_Core_Model_Source_Urlrewrite_OptionsTest::getAllOptions
     */
    public function testGetAllOptions()
    {
        $model = new Magento_Core_Model_Source_Urlrewrite_Options();
        $options = $model->getAllOptions();
        $this->assertInternalType('array', $options);
        $expectedOptions = array(
            '' => 'No',
            'R' => 'Temporary (302)',
            'RP' => 'Permanent (301)'
        );
        $this->assertEquals($expectedOptions, $options);
    }
}
