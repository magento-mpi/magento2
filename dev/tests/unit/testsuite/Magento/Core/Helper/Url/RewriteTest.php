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
 * Test for \Magento\Core\Helper\Url\RewriteTest
 */
namespace Magento\Core\Helper\Url;

class RewriteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Initialize helper
     */
    protected function setUp()
    {
        $optionsModel = new \Magento\Core\Model\Source\Urlrewrite\Options();

        $coreRegisterMock = $this->getMock('Magento\Core\Model\Registry');
        $coreRegisterMock->expects($this->any())
            ->method('registry')
            ->with('_singleton/Magento_Core_Model_Source_Urlrewrite_Options')
            ->will($this->returnValue($optionsModel));

        $objectManagerMock = $this->getMockBuilder('Magento\ObjectManager')->getMock();
        $objectManagerMock->expects($this->any())
            ->method('get')
            ->with('Magento\Core\Model\Registry')
            ->will($this->returnValue($coreRegisterMock));

        \Magento\App\ObjectManager::setInstance($objectManagerMock);
    }

    /**
     * Test hasRedirectOptions
     *
     * @dataProvider redirectOptionsDataProvider
     */
    public function testHasRedirectOptions($option, $expected)
    {
        $optionsMock = $this->getMock('Magento\Core\Model\Source\Urlrewrite\Options', array('getRedirectOptions'),
            array(), '', false, false);
        $optionsMock->expects($this->any())->method('getRedirectOptions')->will($this->returnValue(array('R', 'RP')));
        $helper = new \Magento\Core\Helper\Url\Rewrite(
            $this->getMock('Magento\Core\Helper\Context', array(), array(), '', false, false),
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
        return array(
            array('', false),
            array('R', true),
            array('RP', true),
        );
    }
}
