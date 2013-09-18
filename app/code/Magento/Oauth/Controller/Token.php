<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth token controller
 */
class Magento_Oauth_Controller_Token extends Magento_Core_Controller_Front_Action
{

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /** @var  Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    public function __construct(
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Core_Controller_Varien_Action_Context $context
    ) {
        parent::__construct($context);
        $this->_oauthService = $oauthService;
    }

    /**
     * TODO: Check if this is needed
     * Dispatch event before action
     *
     * @return void
     */
    public function preDispatch()
    {
        $this->setFlag('', self::FLAG_NO_START_SESSION, 1);
        $this->setFlag('', self::FLAG_NO_CHECK_INSTALLATION, 1);
        $this->setFlag('', self::FLAG_NO_COOKIES_REDIRECT, 0);
        $this->setFlag('', self::FLAG_NO_PRE_DISPATCH, 1);

        parent::preDispatch();
    }

    /**
     * Action to intercept and process Access Token requests
     */
    public function accessAction()
    {
        try {
            $accessTokenReqArray = $this->_prepareTokenRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($accessTokenReqArray);

        } catch (Exception $exception) {
            //TODO: Fix to remove dependency from oauthService for errorMap
            $response = $this->reportProblem(
                $this->_oauthService->getErrorMap(),
                $this->_oauthService->getErrorToHttpCodeMap(),
                $exception
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

    /**
     * TODO: Do we need to rename this operation to pre-authorize :
     * https://wiki.corp.x.com/display/MDS/Web+API+Authentication?focusedCommentId=80728936#comment-80728936
     * Action to intercept and process Request Token requests
     */
    public function requestAction()
    {
        try {

            $signedRequest = $this->_prepareTokenRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getRequestToken($signedRequest);

        } catch (Exception $exception) {
            //TODO: Fix to remove dependency from oauthService for errorMap
            $response = $this->reportProblem(
                $this->_oauthService->getErrorMap(),
                $this->_oauthService->getErrorToHttpCodeMap(),
                $exception
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }


    /**
     *
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
    public function _prepareTokenRequest($httpRequest)
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

    /**
     * Create response string for problem during request and set HTTP error code
     *
     * @param array $errorMap
     * @param array $errorsToHttpCode
     * @param Exception $exception
     * @param Zend_Controller_Response_Http $response OPTIONAL If NULL - will use internal getter
     * @return string
     */
    public function reportProblem(
        $errorMap = array(),
        $errorsToHttpCode = array(),
        Exception $exception,
        Zend_Controller_Response_Http $response = null
    ) {
        $eMsg = $exception->getMessage();

        if ($exception instanceof Magento_Oauth_Exception) {
            $eCode = $exception->getCode();

            if (isset($errorMap[$eCode])) {
                $errorMsg = $errorMap[$eCode];
                $responseCode = $errorsToHttpCode[$eCode];
            } else {
                $errorMsg = 'unknown_problem&code=' . $eCode;
                $responseCode = self::HTTP_INTERNAL_ERROR;
            }
            if (Magento_Oauth_Service_OauthV1Interface::ERR_PARAMETER_ABSENT == $eCode) {
                $errorMsg .= '&oauth_parameters_absent=' . $eMsg;
            } elseif ($eMsg) {
                $errorMsg .= '&message=' . $eMsg;
            }
        } else {
            $errorMsg = 'internal_error&message=' . ($eMsg ? $eMsg : 'empty_message');
            $responseCode = self::HTTP_INTERNAL_ERROR;
        }
        if (!$response) {
            $response = $this->getResponse();
        }
        $response->setHttpResponseCode($responseCode);

        return array('oauth_problem' => $errorMsg);
    }

}