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
 * Test class for \Magento\Core\Model\Layout\Argument\HandlerAbstract
 */
namespace Magento\Core\Model\Layout\Argument;

class HandlerAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject|\Magento\Core\Model\Layout\Argument\HandlerAbstract */
    protected $_model;

    public function setUp()
    {
        $this->_model = $this->getMockForAbstractClass(
            'Magento\Core\Model\Layout\Argument\HandlerAbstract',
            array(), '', true
        );
    }

    /**
     * @param \Magento\Core\Model\Layout\Element $argument
     * @param array $expectedResult
     * @dataProvider parseDataProvider
     */
    public function testParse($argument, $expectedResult)
    {
        $result = $this->_model->parse($argument);
        if (isset($result['updaters'])) {
            $result['updaters'] = array_values($result['updaters']);
        }
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
            'Magento\Core\Model\Layout\Element'
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
