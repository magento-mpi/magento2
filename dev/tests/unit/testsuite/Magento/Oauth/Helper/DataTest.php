<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Oauth_Helper_DataTest extends PHPUnit_Framework_TestCase
{
     /** @var Magento_Core_Helper_Context */
    protected $_coreContextMock;

    /** @var Magento_Oauth_Helper_Data */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento_Core_Helper_Context')
            ->disableOriginalConstructor()
            ->getMock();


        $this->_oauthHelper = new Magento_Oauth_Helper_Data(
            $this->_coreContextMock
        );
    }

    protected function tearDown()
    {
        unset($this->_coreContextMock);
        unset($this->_oauthHelper);
    }

    /**
     * @dataProvider dataProviderForPrepareErrorResponseTest
     */
    public function testPrepareErrorResponse($exception, $response, $expected)
    {
        /* @var $response Zend_Controller_Response_Http */
        $errorResponse = $this->_oauthHelper->prepareErrorResponse($exception, $response);
        $this->assertEquals(['oauth_problem' => $expected[0]], $errorResponse);
        $this->assertEquals($expected[1], $response->getHttpResponseCode());
    }

    public function dataProviderForPrepareErrorResponseTest()
    {
        return [
            [
                new Magento_Oauth_Exception('msg', Magento_Oauth_Helper_Service::ERR_VERSION_REJECTED),
                new Zend_Controller_Response_Http(),
                ['version_rejected&message=msg', Magento_Oauth_Helper_Data::HTTP_BAD_REQUEST]
            ],
            [
                new Magento_Oauth_Exception('msg', 255),
                new Zend_Controller_Response_Http(),
                ['unknown_problem&code=255&message=msg', Magento_Oauth_Helper_Data::HTTP_INTERNAL_ERROR]
            ],
            [
                new Magento_Oauth_Exception('param', Magento_Oauth_Helper_Service::ERR_PARAMETER_ABSENT),
                new Zend_Controller_Response_Http(),
                ['parameter_absent&oauth_parameters_absent=param', Magento_Oauth_Helper_Data::HTTP_BAD_REQUEST]
            ],
            [
                new Exception('msg'),
                new Zend_Controller_Response_Http(),
                ['internal_error&message=msg', Magento_Oauth_Helper_Data::HTTP_INTERNAL_ERROR]
            ],
            [
                new Exception(),
                new Zend_Controller_Response_Http(),
                ['internal_error&message=empty_message', Magento_Oauth_Helper_Data::HTTP_INTERNAL_ERROR]
            ],
        ];
    }
}
