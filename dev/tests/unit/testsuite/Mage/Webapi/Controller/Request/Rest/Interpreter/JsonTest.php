<?php
/**
 * Test Webapi Json Interpreter Request Rest Controller
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Model_Factory_Helper */
    protected $_helperFactoryMock;

    /** @var Mage_Webapi_Controller_Request_Rest_Interpreter_Json */
    protected $_jsonInterpreter;

    /** @var Mage_Core_Helper_Data */
    protected $_coreHelperMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper');
        $this->_coreHelperMock = $this->getMock('Mage_Core_Helper_Data', array('__'));
        $this->_coreHelperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_helperFactoryMock->expects($this->any())->method('get')
            ->will($this->returnValue($this->_coreHelperMock));
        /** Initialize SUT. */
        $this->_jsonInterpreter = new Mage_Webapi_Controller_Request_Rest_Interpreter_Json($this->_helperFactoryMock);
        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_helperFactoryMock);
        unset($this->_jsonInterpreter);
        unset($this->_coreHelperMock);
        parent::tearDown();
    }

    public function testInterpretInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid data type "boolean". String expected.');
        $this->_jsonInterpreter->interpret(false);
    }

    public function testInterpret()
    {
        $expectedArray = array(
            'optionOne' => 'test',
            array(
                'subOptionOne' => '1',
                'subOptionTwo' => '2',
            ),
            array(
                'subOptionThree' => '3',
            )
        );
        $inputValidJson = '{
           "optionOne":"test",
           "0":{
              "subOptionOne":"1",
              "subOptionTwo":"2"
           },
           "1":{
              "subOptionThree":"3"
           }
        }';
        $this->assertEquals(
            $expectedArray,
            $this->_jsonInterpreter->interpret(
                $inputValidJson,
                'Invalid interpretation from json to array.'
            )
        );
    }

    public function testInterpretInvalidEncodedBodyException()
    {
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Decoding error.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $inputInvalidJson = 'invalid';
        $this->_jsonInterpreter->interpret($inputInvalidJson);
    }
}



