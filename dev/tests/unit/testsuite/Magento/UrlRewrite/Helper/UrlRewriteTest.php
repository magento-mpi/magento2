<?php
/**
 * {license_notice}
 *
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\UrlRewrite\Helper;

class UrlRewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test hasRedirectOptions
     *
     * @dataProvider redirectOptionsDataProvider
     */
    public function testHasRedirectOptions($option, $expected)
    {
        $optionsMock = $this->getMock(
            'Magento\UrlRewrite\Model\UrlRewrite\OptionProvider',
            array('getRedirectOptions'),
            array(),
            '',
            false,
            false
        );
        $optionsMock->expects($this->any())->method('getRedirectOptions')->will($this->returnValue(array('R', 'RP')));
        $helper = new \Magento\UrlRewrite\Helper\UrlRewrite(
            $this->getMock('Magento\App\Helper\Context', array(), array(), '', false, false),
            $optionsMock
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
        return array(array('', false), array('R', true), array('RP', true));
    }
}
