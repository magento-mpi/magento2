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
 * Test for Magento_Core_Helper_Url_RewriteTest
 */
class Magento_Core_Helper_Url_RewriteTest extends PHPUnit_Framework_TestCase
{
    /**
     * Initialize helper
     */
    protected function setUp()
    {
        $helper = $this->getMockBuilder('Magento_Adminhtml_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));
        Mage::register('_helper/Magento_Adminhtml_Helper_Data', $helper);

        $optionsModel = new Magento_Core_Model_Source_Urlrewrite_Options();
        Mage::register('_singleton/Magento_Core_Model_Source_Urlrewrite_Options', $optionsModel);
    }

    /**
     * Clear helper
     */
    protected function tearDown()
    {
        Mage::unregister('_helper/Magento_Adminhtml_Helper_Data');
        Mage::unregister('_singleton/Magento_Core_Model_Source_Urlrewrite_Options');
    }

    /**
     * Test hasRedirectOptions
     *
     * @dataProvider redirectOptionsDataProvider
     */
    public function testHasRedirectOptions($option, $expected)
    {
        $helper = new Magento_Core_Helper_Url_Rewrite(
            $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false)
        );
        $mockObject = new Magento_Object();
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
