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
 * Test class for \Magento\Core\Model\Layout\Argument\Handler\String
 */
namespace Magento\Core\Model\Layout\Argument\Handler;

class StringTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\Handler\Boolean
     */
    protected $_model;

    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $helperObjectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_objectManagerMock = $this->getMock('Magento\ObjectManager');
        $this->_model = $helperObjectManager->getObject(
            'Magento\Core\Model\Layout\Argument\Handler\String',
            array('objectManager' => $this->_objectManagerMock)
        );
    }

    /**
     * @dataProvider parseDataProvider
     * @param Magento_Core_Model_Layout_Element $argument
     * @param array $expectedResult
     */
    public function testParse($argument, $expectedResult)
    {
        $result = $this->_model->parse($argument);
        $this->assertEquals($result, $expectedResult);
    }

    /**
     * @return array
     */
    public function parseDataProvider()
    {
        $layout = simplexml_load_file(
            __DIR__ . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'arguments.xml',
            'Magento\Core\Model\Layout\Element'
        );
        $result = $this->processDataProvider();
        $simpleString = $layout->xpath('//argument[@name="testSimpleString"]');
        $translateString = $layout->xpath('//argument[@name="testTranslateString"]');
        $complexString = $layout->xpath('//argument[@name="testComplexString"]');
        return array(
            array($simpleString[0], $result[0][0] + array('type' => 'string')),
            array($translateString[0], $result[1][0] + array('type' => 'string')),
            array($complexString[0], $result[2][0] + array('type' => 'string')),
        );
    }

    /**
     * @dataProvider processDataProvider
     * @param array $argument
     * @param boolean $expectedResult
     */
    public function testProcess($argument, $expectedResult)
    {
        $result = $this->_model->process($argument);
        $this->assertEquals($result, $expectedResult);
        if (!empty($argument['value']['translate'])) {
            $this->assertInstanceOf('Magento\Phrase', $result);
        }
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return array(
            array(array('value' => array('string' => 'Simple Test')), 'Simple Test'),
            array(array('value' => array('string' => 'Test Translate', 'translate' => true)), 'Test Translate'),
            array(array('value' => array('string' => 'Complex Test')), 'Complex Test'),
        );
    }

    /**
     * @dataProvider processExceptionDataProvider
     * @param array $argument
     * @param string $message
     */
    public function testProcessException($argument, $message)
    {
        $this->setExpectedException(
            'InvalidArgumentException', $message
        );
        $this->_model->process($argument);
    }

    /**
     * @return array
     */
    public function processExceptionDataProvider()
    {
        return array(
            array(array('value' => null), 'Value is required for argument'),
            array(array('value' => array()), 'Passed value has incorrect format'),
            array(array('value' => array('string' => false)), 'Value is not string argument'),
        );
    }
}
