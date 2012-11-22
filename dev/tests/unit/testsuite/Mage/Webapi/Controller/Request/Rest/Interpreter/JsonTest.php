<?php
/**
 * Test Webapi Json Interpreter Request Rest Controller
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Request_Rest_Interpreter_JsonTest extends PHPUnit_Framework_TestCase
{
    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_Factory_Helper */
    protected $_helperFactoryMock;

    /** @var Mage_Webapi_Controller_Request_Rest_Interpreter_Json */
    protected $_jsonInterpreter;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Helper_Data */
    protected $_coreHelperMock;

    /** @var PHPUnit_Framework_MockObject_MockObject|Mage_Core_Model_App */
    protected $_appMock;

    protected function setUp()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper');
        $this->_coreHelperMock = $this->getMock('Mage_Core_Helper_Data', array('__'));
        $this->_coreHelperMock->expects($this->any())->method('__')->will($this->returnArgument(0));
        $this->_helperFactoryMock->expects($this->any())->method('get')
            ->will($this->returnValue($this->_coreHelperMock));
        $this->_appMock = $this->getMockBuilder('Mage_Core_Model_App')
            ->setMethods(array('isDeveloperMode'))
            ->disableOriginalConstructor()
            ->getMock();
        /** Initialize SUT. */
        $this->_jsonInterpreter = new Mage_Webapi_Controller_Request_Rest_Interpreter_Json(
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
        parent::tearDown();
    }

    public function testInterpretInvalidArgumentException()
    {
        $this->setExpectedException('InvalidArgumentException', 'Invalid data type "boolean". String is expected.');
        $this->_jsonInterpreter->interpret(false);
    }

    /**
     * Test for json Interpret() method
     *
     * @dataProvider dataProviderSuccess
     * @param $inputEncodedJson
     * @param $expectedDecodedJson
     */
    public function testInterpret($inputEncodedJson, $expectedDecodedJson)
    {
        $this->assertEquals(
            $expectedDecodedJson,
            $this->_jsonInterpreter->interpret($inputEncodedJson),
            'Invalid interpretation from json to array.'
        );
    }

    public function testInterpretInvalidEncodedBodyExceptionDeveloperModeOn()
    {
        /** Prepare mocks for SUT constructor. */
        $this->_appMock->expects($this->once())
            ->method('isDeveloperMode')
            ->will($this->returnValue(false));
        $this->setExpectedException(
            'Mage_Webapi_Exception',
            'Decoding error.',
            Mage_Webapi_Exception::HTTP_BAD_REQUEST
        );
        $inputInvalidJson = 'invalid';
        $this->_jsonInterpreter->interpret($inputInvalidJson);
    }

    /**
     * Provide data for testing successful flow
     *
     * @return array
     */
    public function dataProviderSuccess()
    {
        return array(
            'Test mixed json array value.' => array(
                '{"0":"assoc_item1","1":"assoc_item2","assoc:test001":'
                    . '"<some01>text<\\/some01>","assoc.test002":"1 > 0","assoc_test003.":"chars ]]>","assoc_test004"'
                    . ':"chars  !\"#$%&\'()*+,\/;<=>?@[\\\]^`{|}~  chars ","key chars `\\\\\/;:][{}\"|\'.,~!@#$%^&*()'
                    . '_+":"chars"}',
                array(
                    'assoc_item1',
                    'assoc_item2',
                    'assoc:test001' => '<some01>text</some01>',
                    'assoc.test002' => '1 > 0',
                    'assoc_test003.' => 'chars ]]>',
                    'assoc_test004' => 'chars  !"#$%&\'()*+,/;<=>?@[\]^`{|}~  chars ',
                    'key chars `\/;:][{}"|\'.,~!@#$%^&*()_+' => 'chars',
                )
            ),
            'Test associative json array value.' => array(
                '{"key1":"test1","key2":"test2","array":{"test01":"some1","test02":"some2"}}',
                array(
                    'key1' => 'test1',
                    'key2' => 'test2',
                    'array' => array(
                        'test01' => 'some1',
                        'test02' => 'some2',
                    )
                )
            ),
            'Test null value.' => array('null', null),
            'Test "true" value.' => array('true', true),
            'Test numeric value.' => array('1', 1),
            'Test float value.' => array('1.234', 1.234),
        );
    }
}



