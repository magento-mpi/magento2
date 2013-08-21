<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth token controller
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_TokenController extends Mage_Core_Controller_Front_Action
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

    /** @var  Mage_Oauth_Service_OauthInterfaceV1 */
    protected $_oauthService;

    public function __construct(
        Mage_Oauth_Service_OauthInterfaceV1 $oauthService,
        Mage_Core_Controller_Varien_Action_Context $context,
        $areaCode = null
    ) {
        $this->_oauthService = $oauthService;
        parent::__construct($context, $areaCode);
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
            $accessTokenReqArray = $this->_prepareTokenRequest();

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

            $signedRequest = $this->_prepareTokenRequest();

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
     * Add and create array of parameters needed by the token services
     *
     * @return array
     */
    public function _prepareTokenRequest()
    {
        //Fetch and populate protocol information from request body and header into this controller class variables
        $requestArray = $this->_fetchParams();

        //TODO: Fix needed for $this->getRequest()->getHttpHost(). Hosts with port are not covered
        $requestUrl = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() .
            $this->getRequest()->getRequestUri();

        $requestArray['request_url'] = $requestUrl;
        $requestArray['http_method'] = $this->getRequest()->getMethod();
        return $requestArray;
    }


    /**
     * TODO: FIX this method. Also refactor and break it down further
     * Retrieve protocol and request parameters from request object
     *
     * @link http://tools.ietf.org/html/rfc5849#section-3.5
     * @return array $protocolParams array of merged parameters from header, request body and/or query param
     */
    protected function _fetchParams()
    {
        $protocolParams = array();

        $authHeaderValue = $this->getRequest()->getHeader('Authorization');

        if ($authHeaderValue && 'oauth' === strtolower(substr($authHeaderValue, 0, 5))) {
            $authHeaderValue = substr($authHeaderValue, 6); // ignore 'OAuth ' at the beginning

            foreach (explode(',', $authHeaderValue) as $paramStr) {
                $nameAndValue = explode('=', trim($paramStr), 2);

                if (count($nameAndValue) < 2) {
                    continue;
                }
                if ($this->_isProtocolParameter($nameAndValue[0])) {
                    $protocolParams[rawurldecode($nameAndValue[0])] = rawurldecode(trim($nameAndValue[1], '"'));
                }
            }
        }
        $contentTypeHeader = $this->getRequest()->getHeader(Zend_Http_Client::CONTENT_TYPE);

        if ($contentTypeHeader && 0 === strpos($contentTypeHeader, Zend_Http_Client::ENC_URLENCODED)) {
            $protocolParamsNotSet = !$protocolParams;

            parse_str($this->getRequest()->getRawBody(), $protocolParams);

            foreach ($protocolParams as $bodyParamName => $bodyParamValue) {
                if (!$this->_isProtocolParameter($bodyParamName)) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                } elseif ($protocolParamsNotSet) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                }
            }
        }
        $protocolParamsNotSet = !$protocolParams;

        $url = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() . $this->getRequest()
                ->getRequestUri();

        if (($queryString = Zend_Uri_Http::fromString($url)->getQuery())) {
            foreach (explode('&', $queryString) as $paramToValue) {
                $paramData = explode('=', $paramToValue);

                if (2 === count($paramData) && !$this->_isProtocolParameter($paramData[0])) {
                    $protocolParams[rawurldecode($paramData[0])] = rawurldecode($paramData[1]);
                }
            }
        }
        if ($protocolParamsNotSet) {
            $this->_fetchProtocolParamsFromQuery($protocolParams);
        }

        //Combine request and header parameters
        return $protocolParams;
    }

    /**
     * Retrieve protocol parameters from query string
     *
     * @param $protocolParams
     */
    protected function _fetchProtocolParamsFromQuery(&$protocolParams)
    {
        foreach ($this->getRequest()->getQuery() as $queryParamName => $queryParamValue) {
            if ($this->_isProtocolParameter($queryParamName)) {
                $protocolParams[$queryParamName] = $queryParamValue;
            }
        }
    }

    /**
     * Check if attribute is oAuth related
     *
     * @param string $attrName
     * @return bool
     */
    protected function _isProtocolParameter($attrName)
    {
        return (bool)preg_match('/oauth_[a-z_-]+/', $attrName);
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

        if ($exception instanceof Mage_Oauth_Exception) {
            $eCode = $exception->getCode();

            if (isset($errorMap[$eCode])) {
                $errorMsg = $errorMap[$eCode];
                $responseCode = $errorsToHttpCode[$eCode];
            } else {
                $errorMsg = 'unknown_problem&code=' . $eCode;
                $responseCode = self::HTTP_INTERNAL_ERROR;
            }
            if (Mage_Oauth_Service_OauthInterfaceV1::ERR_PARAMETER_ABSENT == $eCode) {
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