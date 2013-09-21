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
    /** @var  Magento_Oauth_Service_OauthV1Interface */
    protected $_oauthService;

    /** @var  Magento_Oauth_Helper_Data */
    protected $_helper;

    /**
     * @param Magento_Oauth_Service_OauthV1Interface $oauthService
     * @param Magento_Core_Controller_Varien_Action_Context $context
     * @param Magento_Oauth_Helper_Data $helper
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Context $context,
        Magento_Oauth_Service_OauthV1Interface $oauthService,
        Magento_Oauth_Helper_Data $helper
    ) {
        parent::__construct($context);
        $this->_oauthService = $oauthService;
        $this->_helper = $helper;
    }

    /**
     *  Initiate RequestToken request operation
     */
    public function requestAction()
    {
        try {
            $request = $this->_helper->_prepareServiceRequest($this->getRequest());

            //Request request token
            $response = $this->_oauthService->getRequestToken($request);

        } catch (Exception $exception) {
            $response = $this->_helper->_prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

    /**
     * Initiate AccessToken request operation
     */
    public function accessAction()
    {
        try {
            $request = $this->_helper->_prepareServiceRequest($this->getRequest());

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($request);

        } catch (Exception $exception) {
            $response = $this->_helper->_prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }

}
