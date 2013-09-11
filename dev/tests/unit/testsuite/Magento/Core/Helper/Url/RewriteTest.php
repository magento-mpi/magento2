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
        $optionsModel = new Magento_Core_Model_Source_Urlrewrite_Options();

        $coreRegisterMock = $this->getMock('Magento_Core_Model_Registry');
        $coreRegisterMock->expects($this->any())
            ->method('registry')
            ->with('_singleton/Magento_Core_Model_Source_Urlrewrite_Options')
            ->will($this->returnValue($optionsModel));

        $objectManagerMock = $this->getMockBuilder('Magento_ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento_Core_Model_Registry')
            ->will($this->returnValue($coreRegisterMock));

        Mage::reset();
        Magento_Core_Model_ObjectManager::setInstance($objectManagerMock);
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
