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

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helper;

    public function __construct(
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Oauth_Helper_Data $helper
    ) {
        parent::__construct($context);
        $this->_oauthService = $oauthService;
        $this->_helper = $helper;
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
            $accessTokenReqArray = $this->_helper->_prepareTokenRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($accessTokenReqArray);

        } catch (Exception $exception) {
            $response = $this->_helper->reportProblem(
                $exception,
                $this->getResponse()
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

            $signedRequest = $this->_helper->_prepareTokenRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getRequestToken($signedRequest);

        } catch (Exception $exception) {
            $response = $this->_helper->reportProblem(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

}