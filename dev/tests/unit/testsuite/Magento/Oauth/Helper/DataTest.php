<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Oauth\Helper;

class DataTest extends \PHPUnit_Framework_TestCase
{
     /** @var \Magento\Core\Helper\Context */
    protected $_coreContextMock;

    /** @var \Magento\Oauth\Helper\Data */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_coreContextMock = $this->getMockBuilder('Magento\Core\Helper\Context')
            ->disableOriginalConstructor()
            ->getMock();


        $this->_oauthHelper = new \Magento\Oauth\Helper\Data(
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
        /* @var $response \Zend_Controller_Response_Http */
        $errorResponse = $this->_oauthHelper->prepareErrorResponse($exception, $response);
        $this->assertEquals(['oauth_problem' => $expected[0]], $errorResponse);
        $this->assertEquals($expected[1], $response->getHttpResponseCode());
    }

    public function dataProviderForPrepareErrorResponseTest()
    {
        return [
            [
                new \Magento\Oauth\Exception('msg', \Magento\Oauth\Service\OauthV1Interface::ERR_VERSION_REJECTED),
                new \Zend_Controller_Response_Http(),
                ['version_rejected&message=msg', \Magento\Oauth\Helper\Data::HTTP_BAD_REQUEST]
            ],
            [
                new \Magento\Oauth\Exception('msg', 255),
                new \Zend_Controller_Response_Http(),
                ['unknown_problem&code=255&message=msg', \Magento\Oauth\Helper\Data::HTTP_INTERNAL_ERROR]
            ],
            [
                new \Magento\Oauth\Exception('param', \Magento\Oauth\Service\OauthV1Interface::ERR_PARAMETER_ABSENT),
                new \Zend_Controller_Response_Http(),
                ['parameter_absent&oauth_parameters_absent=param', \Magento\Oauth\Helper\Data::HTTP_BAD_REQUEST]
            ],
            [
                new \Exception('msg'),
                new \Zend_Controller_Response_Http(),
                ['internal_error&message=msg', \Magento\Oauth\Helper\Data::HTTP_INTERNAL_ERROR]
            ],
            [
                new \Exception(),
                new \Zend_Controller_Response_Http(),
                ['internal_error&message=empty_message', \Magento\Oauth\Helper\Data::HTTP_INTERNAL_ERROR]
            ],
        ];
    }
}
