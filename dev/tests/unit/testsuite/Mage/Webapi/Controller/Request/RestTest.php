<?php
/**
 * Test Webapi Request model
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Request_RestTest extends PHPUnit_Framework_TestCase
{
    /**
     * Request mock
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_request;

    protected function setUp()
    {
        parent::setUp();
        /** Prepare mocks for request constructor arguments. */
        $interpreterFactory = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest_Interpreter_Factory')
            ->disableOriginalConstructor()->getMock();
        $helper = $helper = $this->getMockBuilder('Mage_Webapi_Helper_Data')
            ->disableOriginalConstructor()
            ->setMethods(array('__'))
            ->getMock();
        $helper->expects($this->any())->method('__')->will($this->returnArgument(0));
        /** Instantiate request. */
        // TODO: Get rid of SUT mocks.
        $this->_request = $this->getMock('Mage_Webapi_Controller_Request_Rest', array('getHeader', 'getMethod', 'isGet',
            'isPost', 'isPut', 'isDelete'), array($interpreterFactory, $helper));
    }

    protected function tearDown()
    {
        unset($this->_request);
        parent::tearDown();
    }

    /**
     * Test for getAcceptTypes() method
     *
     * @dataProvider providerAcceptType
     * @param string $acceptHeader Value of Accept HTTP header
     * @param array $expectedResult Method call result
     */
    public function testGetAcceptTypes($acceptHeader, $expectedResult)
    {
        $this->_request
            ->expects($this->once())
            ->method('getHeader')
            ->with('Accept')
            ->will($this->returnValue($acceptHeader));
        /** @var Mage_Webapi_Controller_Request_Rest _requestMock */
        $this->assertSame($expectedResult, $this->_request->getAcceptTypes());
    }

    /**
     * Test for getBodyParams() method
     */
    public function testGetBodyParams()
    {
        $this->markTestIncomplete('Bug MAGETWO-692');
        $rawBody = 'a=123&b=145';
        $interpreterMock = $this->getMock('Mage_Webapi_Model_Request_Interpreter_Interface', array('interpret'));
        $requestMock = $this->getMockBuilder('Mage_Webapi_Controller_Request_Rest')
            ->setMethods(array('_getInterpreter','getRawBody'))
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->expects($this->once())
            ->method('getRawBody')
            ->will($this->returnValue($rawBody));

        $requestMock->expects($this->once())
            ->method('_getInterpreter')
            ->will($this->returnValue($interpreterMock));

        $interpreterMock->expects($this->once())
            ->method('interpret')
            ->with($rawBody);

        $requestMock->getBodyParams();
    }

    /**
     * Test for getContentType() method
     *
     * @dataProvider providerContentType
     * @param string $contentTypeHeader 'Content-Type' header value
     * @param string $contentType Appropriate content type for header value
     * @param string|boolean $exceptionMessage Exception message (boolean FALSE if exception is not expected)
     */
    public function testGetContentType($contentTypeHeader, $contentType, $exceptionMessage = false)
    {
        $this->_request
            ->expects($this->once())
            ->method('getHeader')
            ->with('Content-Type')
            ->will($this->returnValue($contentTypeHeader));

        try {
            $this->assertEquals($contentType, $this->_request->getContentType());
        } catch (Mage_Webapi_Exception $e) {
            if ($exceptionMessage) {
                $this->assertEquals(
                    $exceptionMessage, $e->getMessage(), 'Exception message does not match expected one'
                );
                return;
            } else {
                $this->fail('Exception thrown on valid header: ' . $e->getMessage());
            }
        }
        if ($exceptionMessage) {
            $this->fail('Expected exception was not raised');
        }
    }

    /**
     * Test for getOperation() method
     *
     * @dataProvider providerRequestMethod
     * @param string $requestMethod Request method
     * @param string $crudOperation CRUD operation name
     * @param string|boolean $exceptionMessage Exception message (boolean FALSE if exception is not expected)
     */
    public function testGetOperation($requestMethod, $crudOperation, $exceptionMessage = false)
    {
        $expectedMethod = false;
        switch ($requestMethod) {
            case 'GET':
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $expectedMethod = 'is' . ucfirst($requestMethod);
                break;
        }

        if ($expectedMethod) {
            $this->_request
                ->expects($this->once())
                ->method($expectedMethod)
                ->will($this->returnValue(true));
        }

        $this->_request
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($requestMethod));

        try {
            $this->assertEquals($crudOperation, $this->_request->getHttpMethod());
        } catch (Mage_Webapi_Exception $e) {
            if ($exceptionMessage) {
                $this->assertEquals(
                    $exceptionMessage, $e->getMessage(), 'Exception message does not match expected one'
                );
                return;
            } else {
                $this->fail('Exception thrown on valid header: ' . $e->getMessage());
            }
        }
        if ($exceptionMessage) {
            $this->fail('Expected exception was not raised');
        }
    }

    /**
     * Test for getResourceType() method
     *
     */
    public function testGetResourceType()
    {
        $this->assertNull($this->_request->getResourceType());
        $resource = 'test_resource';
        $this->_request->setResourceType($resource);
        $this->assertEquals($resource, $this->_request->getResourceType());
    }

    /**
     * Data provider for testGetAcceptTypes()
     *
     * @return array
     */
    public function providerAcceptType()
    {
        return array(
            // Each element is: array(Accept HTTP header value, expected result))
            array('', array()),
            array(
                'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                array('text/html', 'application/xhtml+xml', 'application/xml', '*/*')
            ),
            array(
                'text/html, application/*, text, */*',
                array('text/html', 'application/*', 'text', '*/*')
            ),
            array(
                'text/html, application/xml;q=0.9, application/xhtml+xml, image/png, image/webp,'
                    . ' image/jpeg, image/gif, image/x-xbitmap, */*;q=0.1',
                array(
                    'text/html', 'application/xhtml+xml', 'image/png', 'image/webp', 'image/jpeg',
                    'image/gif', 'image/x-xbitmap','application/xml',  '*/*')
            )
        );
    }

    /**
     * Data provider for testGetContentType()
     *
     * @return array
     */
    public function providerContentType()
    {
        return array(
            // Each element is: array(Content-Type header value, content-type part[, expected exception message])
            array('', null, 'Content-Type header is empty'),
            array('_?', null, 'Invalid Content-Type header'),
            array('application/x-www-form-urlencoded; charset=UTF-8', 'application/x-www-form-urlencoded'),
            array('application/x-www-form-urlencoded; charset=utf-8', 'application/x-www-form-urlencoded'),
            array('text/html; charset=uTf-8', 'text/html'),
            array('text/html; charset=', null, 'Invalid Content-Type header'),
            array('text/html;', null, 'Invalid Content-Type header'),
            array('application/dialog.dot-info7+xml', 'application/dialog.dot-info7+xml'),
            array('application/x-www-form-urlencoded; charset=cp1251', null, 'UTF-8 is the only supported charset')
        );
    }

    /**
     * Data provider for testGetOperation()
     *
     * @return array
     */
    public function providerRequestMethod()
    {
        return array(
            // Each element is: array(Request method, CRUD operation name[, expected exception message])
            array('INVALID_METHOD', null, 'Invalid request method'),
            array('GET', Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_GET),
            array('POST', Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_CREATE),
            array('PUT', Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_UPDATE),
            array('DELETE', Mage_Webapi_Controller_Request_Rest::HTTP_METHOD_DELETE)
        );
    }

    /**
     * @dataProvider dataProviderTestGetActionTypeByMethod
     * @param string $methodName
     * @param string $expectedActionType
     */
    public function testGetActionTypeByMethod($methodName, $expectedActionType)
    {
        $this->assertEquals(
            $expectedActionType,
            Mage_Webapi_Controller_Request_Rest::getActionTypeByOperation($methodName),
            "Action type was identified incorrectly by method name."
        );
    }

    public static function dataProviderTestGetActionTypeByMethod()
    {
        return array(
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_CREATE,
                Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_COLLECTION
            ),
            array(
                Mage_Webapi_Controller_ActionAbstract::METHOD_DELETE,
                Mage_Webapi_Controller_Request_Rest::ACTION_TYPE_ITEM
            ),
        );
    }

    public function testGetActionTypeException()
    {
        $methodName = 'invalidMethodV1';
        $this->setExpectedException(
            'InvalidArgumentException',
            sprintf('The "%s" method is not a valid resource method.', $methodName)
        );
        Mage_Webapi_Controller_Request_Rest::getActionTypeByOperation($methodName);
    }
}
