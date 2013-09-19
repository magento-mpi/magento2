<?php
/**
 * REST web API authentication model.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Authentication
{
    /** @var Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    /**
     * Initialize dependencies.
     *
     * @param Magento_Oauth_Service_OauthV1Interface $oauthService
     */
    public function __construct(
        Magento_Oauth_Service_OauthV1Interface $oauthService
    ) {
        $this->_oauthService = $oauthService;
    }

    /**
     * Authenticate user.
     *
     * @param Zend_Controller_Request_Http $httpRequest
     * @throws Magento_Webapi_Exception If authentication failed
     */
    public function authenticate($httpRequest)
    {
        try {
            $this->_oauthService->validateAccessToken($this->_extractAuthenticationParams($httpRequest));
        } catch (Exception $e) {
            throw new Magento_Webapi_Exception(
                $e, //TODO : Fix this to report oAUth problem appropriately
                //$this->_oauthServer->reportProblem($e),
                Magento_Webapi_Exception::HTTP_UNAUTHORIZED
            );
        }
    }

    /**
     * Process HTTP request object and prepare for token validation
     *
     * @param Zend_Controller_Request_Http $httpRequest
     * @return array <pre>
     * array (
     * 'request_url' => 'http://magento.ll/oauth/token/access',
     * 'http_method' => 'POST',
     * 'request_parameters' =>
     *       array (
     *          'oauth_header' => 'OAuth oauth_version="1.0", oauth_signature_method="HMAC-SHA1",
     *          oauth_token="a6aa81cc3e65e2960a487939244sssss", oauth_verifier="a6aa81cc3e65e2960a487939244vvvvv",
     *          oauth_nonce="rI7PSWxTZRHWU3R", oauth_timestamp="1377183099",
     *          oauth_consumer_key="a6aa81cc3e65e2960a4879392445e718",
     *          oauth_signature="VNg4mhFlXk7%2FvsxMqqUd5DWIj9s%3D"',
     *
     *          'content_type' => 'text/plain; charset=UTF-8',
     *          'request_body' => false,
     *       )
     * )
     * </pre>
     */
    public function _extractAuthenticationParams($httpRequest)
    {
        $requestArray = array();

        //TODO: Fix needed for $this->getRequest()->getHttpHost(). Hosts with port are not covered
        $requestUrl = $httpRequest->getScheme() . '://' . $httpRequest->getHttpHost() .
            $httpRequest->getRequestUri();

        $requestArray['request_url'] = $requestUrl;
        $requestArray['http_method'] = $httpRequest->getMethod();

        //Fetch and populate protocol information from request body and header into this controller class variables
        $requestParams = array();

        $requestParams['oauth_header'] = $httpRequest->getHeader('Authorization');
        $requestParams['content_type'] = $httpRequest->getHeader(Zend_Http_Client::CONTENT_TYPE);
        $requestParams['request_body'] = $httpRequest->getRawBody();

        $requestArray['request_parameters'] = $requestParams;

        return $requestArray;
    }

}
