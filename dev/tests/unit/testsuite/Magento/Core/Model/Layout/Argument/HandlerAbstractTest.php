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
            array($this->getMock('Magento_ObjectManager')), '', true
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
        $this->assertEquals($expectedResult, $result);
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
        return array(
            array(
                reset($layout->xpath('//argument[@name="testParseWithoutUpdater"]')),
                array(
                    'type' => 'string')
            ),
            array(
                reset($layout->xpath('//argument[@name="testParseWithUpdater"]')),
                array(
                    'type' => 'string',
                    'updaters' => array('Magento_Test_Updater')
                )
            ),
        );
    }
}
