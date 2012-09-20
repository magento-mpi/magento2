<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Webapi
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test Webapi Request model
 */
class Mage_Webapi_Model_Rest_Request_DecoratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * Decorator object
     *
     * @var Mage_Webapi_Model_Rest_Request_Decorator
     */
    protected $_decorator;

    /**
     * Request object
     *
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_requestMock;


    protected function setUp()
    {
        parent::setUp();
        /** @var Mage_Webapi_Model_Request $requestMock */
        $requestMock = $this->getMock('Mage_Webapi_Model_Request', array('getHeader', 'getMethod', 'isGet', 'isPost',
            'isPut', 'isDelete'));
        $this->_requestMock = $requestMock;
        $this->_decorator = new Mage_Webapi_Model_Rest_Request_Decorator($requestMock);
    }

    /**
     * Test for getAcceptTypes() method
     *
     * @dataProvider providerAcceptType
     *
     * @param string $acceptHeader Value of Accpt HTTP header
     * @param array $expectedResult Method call result
     */
    public function testGetAcceptTypes($acceptHeader, $expectedResult)
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('Accept')
            ->will($this->returnValue($acceptHeader));

        $this->assertSame($expectedResult, $this->_decorator->getAcceptTypes());
    }

    /**
     * Test for getBodyParams() method
     */
    public function testGetBodyParams()
    {
        $rawBody = 'a=123&b=145';
        $interpreterMock = $this->getMock('Mage_Webapi_Model_Request_Interpreter_Interface', array('interpret'));
        $requestDecoratorMock = $this->getMockBuilder('Mage_Webapi_Model_Rest_Request_Decorator')
            ->setMethods(array('_getInterpreter','getRawBody'))
            ->disableOriginalConstructor()
            ->getMock();

        $requestDecoratorMock->expects($this->once())
            ->method('getRawBody')
            ->will($this->returnValue($rawBody));

        $requestDecoratorMock->expects($this->once())
            ->method('_getInterpreter')
            ->will($this->returnValue($interpreterMock));

        $interpreterMock->expects($this->once())
            ->method('interpret')
            ->with($rawBody);
        /** @var Mage_Webapi_Model_Rest_Request_Decorator $requestDecoratorMock */
        $requestDecoratorMock->getBodyParams();
    }

    /**
     * Test for getContentType() method
     *
     * @dataProvider providerContentType
     *
     * @param string $contentTypeHeader 'Content-Type' header value
     * @param string $contentType Appropriate content type for header value
     * @param string|boolean $exceptionMessage Exception message (boolean FALSE if exception is not expected)
     */
    public function testGetContentType($contentTypeHeader, $contentType, $exceptionMessage = false)
    {
        $this->_requestMock
            ->expects($this->once())
            ->method('getHeader')
            ->with('Content-Type')
            ->will($this->returnValue($contentTypeHeader));

        try {
            $this->assertEquals($contentType, $this->_decorator->getContentType());
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
     *
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
            $this->_requestMock
                ->expects($this->once())
                ->method($expectedMethod)
                ->will($this->returnValue(true));
        }

        $this->_requestMock
            ->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue($requestMethod));

        try {
            $this->assertEquals($crudOperation, $this->_decorator->getHttpMethod());
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
        $this->assertNull($this->_decorator->getResourceType());
        $resource = 'test_resource';
        $this->_decorator->setResourceType($resource);
        $this->assertEquals($resource, $this->_decorator->getResourceType());
    }

    /**
     * Test for isAssocArrayInRequestBody() method
     */
    public function testIsAssocArrayInRequestBody()
    {
        $rawBodyIsAssocArray = array('key' => 'field');
        $requestDecoratorMock = $this->getMockBuilder('Mage_Webapi_Model_Rest_Request_Decorator')
            ->setMethods(array('getBodyParams'))
            ->disableOriginalConstructor()
            ->getMock();
        $requestDecoratorMock->expects($this->once())
            ->method('getBodyParams')
            ->will($this->returnValue($rawBodyIsAssocArray));
        /** @var Mage_Webapi_Model_Rest_Request_Decorator $requestDecoratorMock */
        $this->assertTrue($requestDecoratorMock->isAssocArrayInRequestBody());

        $requestDecoratorMock = $this->getMockBuilder('Mage_Webapi_Model_Rest_Request_Decorator')
            ->setMethods(array('getBodyParams'))
            ->disableOriginalConstructor()
            ->getMock();
        $rawBodyIsNotAssocArray = array(0 => array('key' => 'field'));
        $requestDecoratorMock->expects($this->once())
            ->method('getBodyParams')
            ->will($this->returnValue($rawBodyIsNotAssocArray));

        $this->assertFalse($requestDecoratorMock->isAssocArrayInRequestBody());
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
            array('GET', Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_GET),
            array('POST', Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_CREATE),
            array('PUT', Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_UPDATE),
            array('DELETE', Mage_Webapi_Controller_Front_Rest::HTTP_METHOD_DELETE)
        );
    }
}
