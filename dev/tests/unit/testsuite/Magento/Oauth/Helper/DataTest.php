<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Helper_DataTest extends PHPUnit_Framework_TestCase
{
    /** @var Magento_Core_Helper_Data */
    protected $_coreHelper;

    /** @var Magento_Core_Helper_Context */
    protected $_coreContextMock;

    /** @var Magento_Core_Model_Store_Config */
    protected $_storeConfigMock;

    /** @var Magento_Oauth_Helper_Data */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento_Core_Helper_Context')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_storeConfigMock = $this->getMockBuilder('Magento_Core_Model_Store_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_coreHelper = new Magento_Core_Helper_Data(
            $this->getMockBuilder('Magento_Core_Model_Event_Manager')->disableOriginalConstructor()->getMock(),
            $this->getMockBuilder('Magento_Core_Helper_Http')->disableOriginalConstructor()->getMock(),
            $this->_coreContextMock,
            $this->getMockBuilder('Magento_Core_Model_Config')->disableOriginalConstructor()->getMock(),
            $this->_storeConfigMock
        );

        $this->_oauthHelper = new Magento_Oauth_Helper_Data(
            $this->_coreHelper,
            $this->_coreContextMock,
            $this->_storeConfigMock
        );
    }

    protected function tearDown()
    {
        unset($this->_coreHelper);
        unset($this->_coreContextMock);
        unset($this->_storeConfigMock);
        unset($this->_oauthHelper);
    }

    public function testGenerateToken()
    {
        $token = $this->_oauthHelper->generateToken();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_TOKEN);
    }

    public function testGenerateTokenSecret()
    {
        $token = $this->_oauthHelper->generateTokenSecret();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_SECRET);
    }

    public function testGenerateVerifier()
    {
        $token = $this->_oauthHelper->generateVerifier();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Token::LENGTH_VERIFIER);
    }

    public function testGenerateConsumerKey()
    {
        $token = $this->_oauthHelper->generateConsumerKey();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Consumer::KEY_LENGTH);
    }

    public function testGenerateConsumerSecret()
    {
        $token = $this->_oauthHelper->generateConsumerSecret();
        $this->assertTrue(is_string($token) && strlen($token) === Magento_Oauth_Model_Consumer::SECRET_LENGTH);
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
                new Magento_Oauth_Exception('msg', Magento_Oauth_Helper_Data::ERR_VERSION_REJECTED),
                new Zend_Controller_Response_Http(),
                ['version_rejected&message=msg', Magento_Oauth_Helper_Data::HTTP_BAD_REQUEST]
            ],
            [
                new Magento_Oauth_Exception('msg', 255),
                new Zend_Controller_Response_Http(),
                ['unknown_problem&code=255&message=msg', Magento_Oauth_Helper_Data::HTTP_INTERNAL_ERROR]
            ],
            [
                new Magento_Oauth_Exception('param', Magento_Oauth_Helper_Data::ERR_PARAMETER_ABSENT),
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
