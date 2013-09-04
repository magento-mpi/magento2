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
 * Test class for Magento_Core_Model_Layout_Argument_Handler_Url
 */
class Magento_Core_Model_Layout_Argument_Handler_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Layout_Argument_Handler_Helper
     */
    protected $_model;

    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManagerMock;

    protected function setUp()
    {
        $helperObjectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_urlModleMock = $this->getMock('Magento_Core_Model_Url');
        $this->_model = $helperObjectManager->getObject(
            'Magento_Core_Model_Layout_Argument_Handler_Url',
            array('urlModel' => $this->_urlModleMock)
        );
    }

    /**
     * @dataProvider parseDataProvider()
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
            'Magento_Core_Model_Layout_Element'
        );
        $result = $this->processDataProvider();
        $resultWithParams = $resultWithoutParams = $result[0][0];
        $resultWithoutParams['value']['params'] = array();
        $argWithParams = $layout->xpath('//argument[@name="testUrlWithParams"]');
        $argWithoutParams = $layout->xpath('//argument[@name="testUrlWithoutParams"]');
        return array(
            array($argWithParams[0], $resultWithParams + array('type' => 'url')),
            array($argWithoutParams[0], $resultWithoutParams + array('type' => 'url')),
        );
    }

    /**
     * @dataProvider processDataProvider
     * @param array $argument
     * @param boolean $expectedResult
     */
    public function testProcess($argument, $expectedResult)
    {
        $this->_urlModleMock->expects($this->once())
            ->method('getUrl')
            ->with($argument['value']['path'], $argument['value']['params'])
            ->will($this->returnValue($expectedResult));

        $this->assertEquals($this->_model->process($argument), $expectedResult);
    }

    /**
     * @return array
     */
    public function processDataProvider()
    {
        return array(
            array(
                array(
                    'value' => array(
                        'path' => 'module/controller/action',
                        'params' => array(
                            'firstParam' => 'firstValue',
                            'secondParam' => 'secondValue',
                        ),
                    )
                )
                , 'test/url'
            ),
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
            array(array(), 'Value is required for url argument'),
            array(array('value' => array()), 'Passed value has incorrect format'),
        );
    }
}
