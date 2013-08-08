<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for Mage_Core_Helper_Url_RewriteTest
 */
class Mage_Core_Helper_Url_RewriteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Initialize helper
     */
    protected function setUp()
    {
        $optionsModel = new Mage_Core_Model_Source_Urlrewrite_Options();
        Mage::register('_singleton/Mage_Core_Model_Source_Urlrewrite_Options', $optionsModel);
    }

    /**
     * Clear helper
     */
    protected function tearDown()
    {
        Mage::unregister('_singleton/Mage_Core_Model_Source_Urlrewrite_Options');
    }

    /**
     * Test hasRedirectOptions
     *
     * @dataProvider redirectOptionsDataProvider
     */
    public function testHasRedirectOptions($option, $expected)
    {
        $helper = new Mage_Core_Helper_Url_Rewrite(
            $this->getMock('Mage_Core_Helper_Context', array(), array(), '', false, false)
        );
        $mockObject = new Varien_Object();
        $mockObject->setOptions($option);
        $this->assertEquals($expected, $helper->hasRedirectOptions($mockObject));
    }

    /**
     * Data provider for redirect options
     *
     * @static
     * @return array
     */
    public static function redirectOptionsDataProvider()
    {
        return array(
            array('', false),
            array('R', true),
            array('RP', true),
        );
    }
}
