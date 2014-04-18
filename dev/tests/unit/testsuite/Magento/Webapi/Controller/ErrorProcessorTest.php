<?php
/**
 * Test Webapi Error Processor.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

use Magento\Webapi\Exception as WebapiException;
use Magento\Webapi\ServiceResourceNotFoundException;
use Magento\Webapi\ServiceAuthorizationException;
use Magento\Webapi\ServiceException;

class ErrorProcessorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ErrorProcessor */
    protected $_errorProcessor;

    /** @var \Magento\Core\Helper\Data */
    protected $_helperMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $_appStateMock;

    /** @var \Magento\Framework\Logger */
    protected $_loggerMock;

    protected function setUp()
    {
        /** Set up mocks for SUT. */
        $this->_helperMock = $this->getMockBuilder(
            'Magento\Core\Helper\Data'
        )->disableOriginalConstructor()->getMock();

        $this->_appStateMock = $this->getMockBuilder('Magento\Framework\App\State')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_loggerMock = $this->getMockBuilder('Magento\Framework\Logger')->disableOriginalConstructor()->getMock();

        $filesystemMock = $this->getMockBuilder('\Magento\Framework\App\Filesystem')
            ->disableOriginalConstructor()
            ->getMock();

        /** Initialize SUT. */
        $this->_errorProcessor = new ErrorProcessor(
            $this->_helperMock,
            $this->_appStateMock,
            $this->_loggerMock,
            $filesystemMock
        );

        parent::setUp();
    }

    protected function tearDown()
    {
        unset($this->_errorProcessor);
        unset($this->_helperMock);
        unset($this->_appStateMock);
        parent::tearDown();
    }

    /**
     * Test render method in JSON format.
     */
    public function testRenderJson()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Assert that jsonEncode method will be executed once. */
        $this->_helperMock->expects(
            $this->once()
        )->method(
            'jsonEncode'
        )->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '{"messages":{"error":[{"code":500,"message":"Message"}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in JSON.');
    }

    /**
     * Callback function for RenderJson and RenderJsonInDeveloperMode tests.
     *
     * Method encodes data to JSON and returns it.
     *
     * @param $data
     * @return string
     */
    public function callbackJsonEncode($data)
    {
        return json_encode($data);
    }

    /**
     * Test render method in JSON format with turned on developer mode.
     */
    public function testRenderJsonInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'json';
        /** Mock app to return enabled developer mode flag. */
        $this->_appStateMock->expects($this->any())->method('getMode')->will($this->returnValue('developer'));
        /** Assert that jsonEncode method will be executed once. */
        $this->_helperMock->expects(
            $this->once()
        )->method(
            'jsonEncode'
        )->will(
            $this->returnCallback(array($this, 'callbackJsonEncode'), $this->returnArgument(0))
        );
        ob_start();
        $this->_errorProcessor->render('Message', 'Message trace.', 401);
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '{"messages":{"error":[{"code":401,"message":"Message","trace":"Message trace."}]}}';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in JSON.');
    }

    /**
     * Test render method in XML format.
     */
    public function testRenderXml()
    {
        $_SERVER['HTTP_ACCEPT'] = 'xml';
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message');
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>500</code>' .
            '<message><![CDATA[Message]]></message></data_item></error></messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in XML.');
    }

    /**
     * Test render method in XML format with turned on developer mode.
     */
    public function testRenderXmlInDeveloperMode()
    {
        $_SERVER['HTTP_ACCEPT'] = 'xml';
        /** Mock app to return enabled developer mode flag. */
        $this->_appStateMock->expects($this->any())->method('getMode')->will($this->returnValue('developer'));
        /** Init output buffering to catch output via echo function. */
        ob_start();
        $this->_errorProcessor->render('Message', 'Trace message.', 401);
        /** Get output buffer. */
        $actualResult = ob_get_contents();
        ob_end_clean();
        $expectedResult = '<?xml version="1.0"?><error><messages><error><data_item><code>401</code><message>' .
            '<![CDATA[Message]]></message><trace><![CDATA[Trace message.]]></trace></data_item></error>' .
            '</messages></error>';
        $this->assertEquals($expectedResult, $actualResult, 'Invalid rendering in XML with turned on developer mode.');
    }

    /**
     * Test default render format is JSON.
     */
    public function testRenderDefaultFormat()
    {
        /** Set undefined rendering format. */
        $_SERVER['HTTP_ACCEPT'] = 'undefined';
        /** Assert that jsonEncode method will be executed at least once. */
        $this->_helperMock->expects($this->atLeastOnce())->method('jsonEncode');
        $this->_errorProcessor->render('Message');
    }

    /**
     * Test maskException method with turned on developer mode.
     */
    public function testMaskExceptionInDeveloperMode()
    {
        /** Mock app isDeveloperMode to return true. */
        $this->_appStateMock->expects($this->once())->method('getMode')->will($this->returnValue('developer'));
        /** Init Logical exception. */
        $errorMessage = 'Error Message';
        $logicalException = new \LogicException($errorMessage);
        /** Assert that Logic exception is converted to WebapiException without message obfuscation. */
        $maskedException = $this->_errorProcessor->maskException($logicalException);
        $this->assertInstanceOf('Magento\Webapi\Exception', $maskedException);
        $this->assertEquals(
            $errorMessage,
            $maskedException->getMessage(),
            'Exception was masked incorrectly in developer mode.'
        );
    }

    /**
     * Test sendResponse method with various exceptions
     *
     * @dataProvider dataProviderForSendResponseExceptions
     */
    public function testMaskException($exception, $expectedHttpCode, $expectedMessage, $expectedCode, $expectedDetails)
    {
        /** Assert that exception was logged. */
        $this->_loggerMock->expects($this->once())->method('logException');
        $maskedException = $this->_errorProcessor->maskException($exception);
        $this->assertMaskedException(
            $maskedException,
            $expectedHttpCode,
            $expectedMessage,
            $expectedCode,
            $expectedDetails
        );
    }

    public function dataProviderForSendResponseExceptions()
    {
        return array(
            'Magento\Service\ResourceNotFoundException' => array(
                new ServiceResourceNotFoundException(
                    'Resource not found',
                    2345,
                    null,
                    array('datail1' => 'value1'),
                    'resourceNotFound',
                    'resource10'
                ),
                WebapiException::HTTP_NOT_FOUND,
                'Resource not found',
                2345,
                array('datail1' => 'value1', 'resource_id' => 'resource10')
            ),
            'Magento_Service_ResourceNotFoundException (Empty message)' => array(
                new ServiceResourceNotFoundException(
                    '',
                    2345,
                    null,
                    array('datail1' => 'value1'),
                    'resourceNotFound',
                    'resource10'
                ),
                WebapiException::HTTP_NOT_FOUND,
                "Resource with ID 'resource10' not found.",
                2345,
                array('datail1' => 'value1', 'resource_id' => 'resource10')
            ),
            'Magento\Webapi\ServiceAuthorizationException' => array(
                new ServiceAuthorizationException(
                    'Service authorization exception',
                    345,
                    null,
                    array(),
                    'authorization',
                    3,
                    4
                ),
                WebapiException::HTTP_UNAUTHORIZED,
                'Service authorization exception',
                345,
                array('user_id' => 3, 'resource_id' => 4)
            ),
            'Magento\Webapi\ServiceAuthorizationException (Empty message)' => array(
                new ServiceAuthorizationException('', 345, null, array(), 'authorization', 3, 4),
                WebapiException::HTTP_UNAUTHORIZED,
                "User with ID '3' is not authorized to access resource with ID '4'.",
                345,
                array('user_id' => 3, 'resource_id' => 4)
            ),
            'Magento\Webapi\ServiceException' => array(
                new ServiceException('Generic service exception', 4567),
                WebapiException::HTTP_BAD_REQUEST,
                'Generic service exception',
                4567,
                array()
            ),
            'Magento\Webapi\ServiceException\With\Parameters' => array(
                new ServiceException('Parameterized service exception', 1234, null, array("P1", "P2")),
                WebapiException::HTTP_BAD_REQUEST,
                'Parameterized service exception',
                1234,
                array("P1", "P2")
            ),
            'Exception' => array(
                new \Exception('Non service exception', 5678),
                WebapiException::HTTP_INTERNAL_ERROR,
                'Internal Error. Details are available in Magento log file. Report ID: webapi-',
                0,
                array()
            )
        );
    }

    /**
     * Assert that masked exception contains expected data.
     *
     * @param \Exception $maskedException
     * @param int $expectedHttpCode
     * @param string $expectedMessage
     * @param int $expectedCode
     * @param array $expectedDetails
     */
    public function assertMaskedException(
        $maskedException,
        $expectedHttpCode,
        $expectedMessage,
        $expectedCode,
        $expectedDetails
    ) {
        /** All masked exceptions must be WebapiException */
        $expectedType = 'Magento\Webapi\Exception';
        $this->assertInstanceOf(
            $expectedType,
            $maskedException,
            "Masked exception type is invalid: expected '{$expectedType}', given '" . get_class(
                $maskedException
            ) . "'."
        );
        /** @var $maskedException WebapiException */
        $this->assertEquals(
            $expectedHttpCode,
            $maskedException->getHttpCode(),
            "Masked exception HTTP code is invalid: expected '{$expectedHttpCode}', " .
            "given '{$maskedException->getHttpCode()}'."
        );
        $this->assertContains(
            $expectedMessage,
            $maskedException->getMessage(),
            "Masked exception message is invalid: expected '{$expectedMessage}', " .
            "given '{$maskedException->getMessage()}'."
        );
        $this->assertEquals(
            $expectedCode,
            $maskedException->getCode(),
            "Masked exception code is invalid: expected '{$expectedCode}', given '{$maskedException->getCode()}'."
        );
        $this->assertEquals($expectedDetails, $maskedException->getDetails(), "Masked exception details are invalid.");
    }
}
