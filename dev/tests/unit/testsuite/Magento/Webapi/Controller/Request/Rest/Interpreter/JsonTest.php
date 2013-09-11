<?php
/**
 * Test Webapi Json Interpreter Request Rest Controller.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Controller_Request_Rest_Interpreter_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_helperFactoryMock;

    /** @var \Magento\Webapi\Controller\Request\Rest\Interpreter\Json */
    protected $_jsonInterpreter;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_coreHelperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject */
    protected $_appMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper');
        $this->_coreHelperMock = $this->getMock('Magento\Core\Helper\Data',
            array('jsonDecode'), array(), '', false, false
        );
        $this->_helperFactoryMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue($this->_coreHelperMock));
        $this->_appMock = $this->getMockBuilder('Magento\Core\Model\App')
            ->setMethods(array('isDeveloperMode'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_jsonInterpreter = new \Magento\Webapi\Controller\Request\Rest\Interpreter\Json(
            $this->_helperFactoryMock,
            $this->_appMock
        );
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperFactoryMock);
        unset($this->_jsonInterpreter);
        unset($this->_coreHelperMock);
        unset($this->_appMock);
        parent::tearDown();
    }

    public function testInterpretInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException', '"boolean" data type is invalid. String is expected.');
        $this->_jsonInterpreter->interpret(false);
    }

    public function testInterpret()
    {
        /** Prepare mocks for SUT constructor. */
        $inputEncodedJson = '{"key1":"test1","key2":"test2","array":{"test01":"some1","test02":"some2"}}';
        $expectedDecodedJson = array(
            'key1' => 'test1',
            'key2' => 'test2',
            'array' => array(
                'test01' => 'some1',
                'test02' => 'some2',
            )
        );
        $this->_coreHelperMock->expects($this->once())
            ->method('jsonDecode')
            ->will($this->returnValue($expectedDecodedJson));
        /** Initialize SUT. */
        $this->assertEquals(
            $expectedDecodedJson,
            $this->_jsonInterpreter->interpret($inputEncodedJson),
            'Interpretation from JSON to array is invalid.'
        );
    }

    public function testInterpretInvalidEncodedBodyExceptionDeveloperModeOn()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_coreHelperMock->expects($this->once())
            ->method('jsonDecode')
            ->will($this->throwException(new Zend_Json_Exception));
        $this->_appMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(false));
        $this->setExpectedException(
            '\Magento\Webapi\Exception',
            'Decoding error.',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        /** Initialize SUT. */
        $inputInvalidJson = '{"key1":"test1"."key2":"test2"}';
        $this->_jsonInterpreter->interpret($inputInvalidJson);
    }

    public function testInterpretInvalidEncodedBodyExceptionDeveloperModeOff()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_coreHelperMock->expects($this->once())
            ->method('jsonDecode')
            ->will(
            $this->throwException(
                new Zend_Json_Exception('Decoding error:' . PHP_EOL . 'Decoding failed: Syntax error')
            )
        );
        $this->_appMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(true));
        $this->setExpectedException(
            '\Magento\Webapi\Exception',
            'Decoding error:' . PHP_EOL . 'Decoding failed: Syntax error',
            \Magento\Webapi\Exception::HTTP_BAD_REQUEST
        );
        /** Initialize SUT. */
        $inputInvalidJson = '{"key1":"test1"."key2":"test2"}';
        $this->_jsonInterpreter->interpret($inputInvalidJson);
    }
}



