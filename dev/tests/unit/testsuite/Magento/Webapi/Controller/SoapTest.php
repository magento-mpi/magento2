<?php
/**
 * Test SOAP controller class.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Controller;

class SoapTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Webapi\Controller\Soap */
    protected $_soapController;

    /** @var \Magento\Webapi\Model\Soap\Server */
    protected $_soapServerMock;

    /** @var \Magento\Webapi\Model\Soap\Wsdl\Generator */
    protected $_wsdlGeneratorMock;

    /** @var \Magento\Webapi\Controller\Soap\Request */
    protected $_requestMock;

    /** @var \Magento\Webapi\Controller\Response */
    protected $_responseMock;

    /** @var \Magento\Webapi\Controller\ErrorProcessor */
    protected $_errorProcessorMock;

    /** @var \Magento\App\State */
    protected $_appStateMock;

    /** @var \Magento\Core\Model\App */
    protected $_applicationMock;

    /** @var \Magento\Oauth\Oauth */
    protected $_oauthServiceMock;

    /**
     * Set up Controller object.
     */
    protected function setUp()
    {
        parent::setUp();

        $this->_soapServerMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Server')
            ->disableOriginalConstructor()
            ->setMethods(array('getApiCharset', 'generateUri', 'handle'))
            ->getMock();
        $this->_wsdlGeneratorMock = $this->getMockBuilder('Magento\Webapi\Model\Soap\Wsdl\Generator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock();
        $this->_requestMock = $this->getMockBuilder('Magento\Webapi\Controller\Soap\Request')
            ->disableOriginalConstructor()
            ->setMethods(array('getParam', 'getRequestedServices'))
            ->getMock();
        $this->_responseMock = $this->getMockBuilder('Magento\Webapi\Controller\Response')
            ->disableOriginalConstructor()
            ->setMethods(array('clearHeaders', 'setHeader', 'sendResponse'))
            ->getMock();
        $this->_errorProcessorMock = $this->getMockBuilder('Magento\Webapi\Controller\ErrorProcessor')
            ->disableOriginalConstructor()
            ->setMethods(array('maskException'))
            ->getMock();
        $this->_appStateMock =  $this->getMockBuilder('Magento\App\State')
            ->disableOriginalConstructor()
            ->getMock();
        $localeMock =  $this->getMockBuilder('Magento\Core\Model\Locale')
            ->disableOriginalConstructor()
            ->setMethods(array('getLocale', 'getLanguage'))
            ->getMock();
        $localeMock->expects($this->any())->method('getLocale')->will($this->returnValue($localeMock));
        $localeMock->expects($this->any())->method('getLanguage')->will($this->returnValue('en'));

        $this->_applicationMock =  $this->getMockBuilder('Magento\Core\Model\App')
            ->disableOriginalConstructor()
            ->setMethods(array('getLocale', 'isDeveloperMode'))
            ->getMock();
        $this->_applicationMock->expects($this->any())->method('getLocale')->will($this->returnValue($localeMock));
        $this->_applicationMock->expects($this->any())->method('isDeveloperMode')->will($this->returnValue(false));

        $this->_oauthServiceMock = $this->getMockBuilder('Magento\Oauth\Oauth')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_responseMock->expects($this->any())->method('clearHeaders')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setWSDL')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setEncoding')->will($this->returnSelf());
        $this->_soapServerMock->expects($this->any())->method('setReturnResponse')->will($this->returnSelf());

        $this->_soapController = new \Magento\Webapi\Controller\Soap(
            $this->_requestMock,
            $this->_responseMock,
            $this->_wsdlGeneratorMock,
            $this->_soapServerMock,
            $this->_errorProcessorMock,
            $this->_appStateMock,
            $this->_applicationMock,
            $this->_oauthServiceMock
        );
    }

    /**
     * Clean up Controller and its dependencies.
     */
    protected function tearDown()
    {
        unset($this->_soapController);
        unset($this->_requestMock);
        unset($this->_responseMock);
        unset($this->_wsdlGeneratorMock);
        unset($this->_soapServerMock);
        unset($this->_errorProcessorMock);
        unset($this->_applicationMock);
        unset($this->_appStateMock);

        parent::tearDown();
    }

    /**
     * Test redirected to install page
     */
    public function testRedirectToInstallPage()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(false));
        $this->_errorProcessorMock->expects($this->any())
            ->method('maskException')
            ->will($this->returnArgument(0));
        $encoding = "utf-8";
        $this->_soapServerMock->expects($this->any())
            ->method('getApiCharset')
            ->will($this->returnValue($encoding));

        $this->_soapController->dispatch($this->_requestMock);
        $expectedMessage = <<<EXPECTED_MESSAGE
<?xml version="1.0" encoding="{$encoding}"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" >
    <env:Body>
        <env:Fault>
            <env:Code>
                <env:Value>env:Sender</env:Value>
            </env:Code>
            <env:Reason>
                <env:Text xml:lang="en">Magento is not yet installed</env:Text>
            </env:Reason>
        </env:Fault>
    </env:Body>
</env:Envelope>
EXPECTED_MESSAGE;

        $this->assertXmlStringEqualsXmlString($expectedMessage, $this->_responseMock->getBody());
    }

    /**
     * Test successful WSDL content generation.
     */
    public function testDispatchWsdl()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_mockGetParam(\Magento\Webapi\Model\Soap\Server::REQUEST_PARAM_WSDL, 1);
        $wsdl = 'Some WSDL content';
        $this->_wsdlGeneratorMock->expects($this->any())
            ->method('generate')
            ->will($this->returnValue($wsdl));

        $this->_soapController->dispatch($this->_requestMock);
        $this->assertEquals($wsdl, $this->_responseMock->getBody());
    }

    /**
     * Test successful SOAP action request dispatch.
     */
    public function testDispatchSoapRequest()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $this->_soapServerMock->expects($this->once())
            ->method('handle');
        $_SERVER['HTTP_AUTHORIZATION'] = 'OAuth access_token';
        $this->_oauthServiceMock->expects($this->once())
            ->method('validateAccessToken')
            ->will($this->returnValue(true));
        $response = $this->_soapController->dispatch($this->_requestMock);
        $this->assertEquals(200, $response->getHttpResponseCode());
        unset($_SERVER['HTTP_AUTHORIZATION']);
    }

    /**
     * Test handling exception during dispatch.
     */
    public function testDispatchWithException()
    {
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));
        $exceptionMessage = 'some error message';
        $exception = new \Magento\Webapi\Exception($exceptionMessage);
        $this->_soapServerMock->expects($this->any())
            ->method('handle')
            ->will($this->throwException($exception));
        $this->_errorProcessorMock->expects($this->any())
            ->method('maskException')
            ->will($this->returnValue($exception));
        $encoding = "utf-8";
        $this->_soapServerMock->expects($this->any())
            ->method('getApiCharset')
            ->will($this->returnValue($encoding));

        $this->_soapController->dispatch($this->_requestMock);

        $expectedMessage = <<<EXPECTED_MESSAGE
<?xml version="1.0" encoding="{$encoding}"?>
<env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" >
   <env:Body>
      <env:Fault>
         <env:Code>
            <env:Value>env:Sender</env:Value>
         </env:Code>
         <env:Reason>
            <env:Text xml:lang="en">some error message</env:Text>
         </env:Reason>
      </env:Fault>
   </env:Body>
</env:Envelope>
EXPECTED_MESSAGE;
        $this->assertXmlStringEqualsXmlString($expectedMessage, $this->_responseMock->getBody());
    }

    /**
     * Mock getParam() of request object to return given value.
     *
     * @param $param
     * @param $value
     */
    protected function _mockGetParam($param, $value)
    {
        $this->_requestMock->expects($this->once())
            ->method('getParam')
            ->with($param)
            ->will($this->returnValue($value));
    }
}
