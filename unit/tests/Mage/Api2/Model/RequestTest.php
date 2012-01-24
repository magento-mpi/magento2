<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Mage_Api2
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test Api2 Request model
 */
class Mage_Api2_Model_RequestUnitTest extends Mage_PHPUnit_TestCase
{
    /**
     * Request object
     *
     * @var Mage_Api2_Model_Request
     */
    protected $_request;

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
            array('GET', Mage_Api2_Model_Resource::OPERATION_RETRIEVE),
            array('POST', Mage_Api2_Model_Resource::OPERATION_CREATE),
            array('PUT', Mage_Api2_Model_Resource::OPERATION_UPDATE),
            array('DELETE', Mage_Api2_Model_Resource::OPERATION_DELETE)
        );
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_request = Mage::getSingleton('api2/request', null);
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
        $_SERVER['HTTP_ACCEPT'] = $acceptHeader;

        $this->assertSame($expectedResult, $this->_request->getAcceptTypes());
    }

    /**
     * Test for getApiType() method
     */
    public function testGetApiType()
    {
        $this->assertNull($this->_request->getApiType());

        $apiType = 'test_api_type';

        $this->_request->setParam('api_type', $apiType);

        $this->assertEquals($apiType, $this->_request->getApiType());
    }

    /**
     * Test for getBodyParams() method
     */
    public function testGetBodyParams()
    {
        $rawBody = 'a=123&b=145';
        $interpreterMock = $this->getMock('Mage_Api2_Model_Request_Interpreter_Interface', array('interpret'));
        $requestMock = $this->getMock('Mage_Api2_Model_Request', array('_getInterpreter', 'getRawBody'));

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
     *
     * @param string $contentTypeHeader 'Content-Type' header value
     * @param string $contentType Appropriate content type for header value
     * @param string|boolean $exceptionMessage Exception message (boolean FALSE if exception is not expected)
     */
    public function testGetContentType($contentTypeHeader, $contentType, $exceptionMessage = false)
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = $contentTypeHeader;

        try {
            $this->assertEquals($contentType, $this->_request->getContentType());
        } catch (Mage_Api2_Exception $e) {
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
     * Test for getModel() method
     */
    public function testGetModel()
    {
        $this->assertNull($this->_request->getModel());

        $model = 'test_model';

        $this->_request->setParam('model', $model);

        $this->assertEquals($model, $this->_request->getModel());
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
        $_SERVER['REQUEST_METHOD'] = $requestMethod;

        try {
            $this->assertEquals($crudOperation, $this->_request->getOperation());
        } catch (Mage_Api2_Exception $e) {
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

        $this->_request->setParam('type', $resource);

        $this->assertEquals($resource, $this->_request->getResourceType());
    }

    /**
     * Test for getVersion() method
     *
     */
    public function testGetVersion()
    {
        $version = '1.1';

        $this->assertFalse($this->_request->getVersion());

        $_SERVER['HTTP_VERSION'] = $version;

        $this->assertEquals($version, $this->_request->getVersion());
    }
}
