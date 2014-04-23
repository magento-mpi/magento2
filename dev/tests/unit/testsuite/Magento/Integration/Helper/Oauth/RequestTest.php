<?php
/**
 * Test WebAPI authentication helper.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Helper\Oauth;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\Framework\Oauth\Helper\Request */
    protected $_oauthHelper;

    protected function setUp()
    {
        $this->_oauthHelper = new \Magento\Framework\Oauth\Helper\Request();
    }

    protected function tearDown()
    {
        unset($this->_oauthHelper);
    }

    /**
     * @dataProvider dataProviderForPrepareErrorResponseTest
     */
    public function testPrepareErrorResponse($exception, $response, $expected)
    {
        /* @var $response \Zend_Controller_Response_Http */
        $errorResponse = $this->_oauthHelper->prepareErrorResponse($exception, $response);
        $this->assertEquals(array('oauth_problem' => $expected[0]), $errorResponse);
        $this->assertEquals($expected[1], $response->getHttpResponseCode());
    }

    public function dataProviderForPrepareErrorResponseTest()
    {
        return array(
            array(
                new \Magento\Framework\Oauth\Exception('msg', \Magento\Framework\Oauth\OauthInterface::ERR_VERSION_REJECTED),
                new \Zend_Controller_Response_Http(),
                array('version_rejected&message=msg', \Magento\Framework\Oauth\Helper\Request::HTTP_BAD_REQUEST)
            ),
            array(
                new \Magento\Framework\Oauth\Exception('msg', 255),
                new \Zend_Controller_Response_Http(),
                array('unknown_problem&code=255&message=msg', \Magento\Framework\Oauth\Helper\Request::HTTP_INTERNAL_ERROR)
            ),
            array(
                new \Magento\Framework\Oauth\Exception('param', \Magento\Framework\Oauth\OauthInterface::ERR_PARAMETER_ABSENT),
                new \Zend_Controller_Response_Http(),
                array(
                    'parameter_absent&oauth_parameters_absent=param',
                    \Magento\Framework\Oauth\Helper\Request::HTTP_BAD_REQUEST
                )
            ),
            array(
                new \Exception('msg'),
                new \Zend_Controller_Response_Http(),
                array('internal_error&message=msg', \Magento\Framework\Oauth\Helper\Request::HTTP_INTERNAL_ERROR)
            ),
            array(
                new \Exception(),
                new \Zend_Controller_Response_Http(),
                array('internal_error&message=empty_message', \Magento\Framework\Oauth\Helper\Request::HTTP_INTERNAL_ERROR)
            )
        );
    }
}
