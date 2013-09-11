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
        $optionsModel = new \Magento\Core\Model\Source\Urlrewrite\Options();
        Mage::register('_singleton/\Magento\Core\Model\Source\Urlrewrite\Options', $optionsModel);
    }

    /**
     * Clear helper
     */
    protected function tearDown()
    {
        Mage::unregister('_singleton/\Magento\Core\Model\Source\Urlrewrite\Options');
    }

    /**
     * Test hasRedirectOptions
     *
     * @dataProvider redirectOptionsDataProvider
     */
    public function testHasRedirectOptions($option, $expected)
    {
        $helper = new \Magento\Core\Helper\Url\Rewrite(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false)
        );
        $mockObject = new \Magento\Object();
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
