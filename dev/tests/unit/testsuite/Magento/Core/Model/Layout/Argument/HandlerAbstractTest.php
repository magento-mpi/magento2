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
 * Test class for Magento_Core_Model_Layout_Argument_HandlerAbstract
 */
class Magento_Core_Model_Layout_Argument_HandlerAbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Magento_Core_Model_Layout_Argument_HandlerAbstract */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento_Core_Model_Layout_Argument_HandlerAbstract',
            array(), '', true
        );
    }

    /**
     * @param Magento_Core_Model_Layout_Element $argument
     * @param array $expectedResult
     * @dataProvider parseDataProvider
     */
    public function testParse($argument, $expectedResult)
    {
        $result = $this->_model->parse($argument);
        $this->_assertArrayContainsArray($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        $layout = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . 'Handler' . DIRECTORY_SEPARATOR
            . '_files' . DIRECTORY_SEPARATOR . 'arguments.xml',
            'Magento_Core_Model_Layout_Element'
        );
        $withoutUpdater = $layout->xpath('//argument[@name="testParseWithoutUpdater"]');
        $withUpdater = $layout->xpath('//argument[@name="testParseWithUpdater"]');
        return array(
            array(
                reset($withoutUpdater),
                array(
                    'type' => 'string'
                )
            ),
            array(
                reset($withUpdater),
                array(
                    'type' => 'string',
                    'updaters' => array('Magento_Test_Updater')
                )
            ),
        );
    }

    /**
     * Asserting that an array contains another array
     *
     * @param array $needle
     * @param array $haystack
     */
    protected function _assertArrayContainsArray(array $needle, array $haystack)
    {
        foreach ($needle as $key => $val) {
            $this->assertArrayHasKey($key, $haystack);

            if (is_array($val)) {
                $this->_assertArrayContainsArray($val, $haystack[$key]);
            } else {
                $this->assertEquals($val, $haystack[$key]);
            }
        }
    }
}
