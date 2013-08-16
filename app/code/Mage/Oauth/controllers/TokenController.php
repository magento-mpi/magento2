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
class Mage_Oauth_TokenController extends Mage_Oauth_Controller_Abstract
{

    /**
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
     * Index action. Process request and response permanent token
     */
    public function indexAction()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                throw Mage::exception('Mage_Oauth', "Only POST allowed on token access", self::HTTP_METHOD_NOT_ALLOWED);
            }

            //Fetch and populate protocol information from request body and header into this controller class variables
            $this->_fetchParams();

            //TODO: Fix needed for $this->getRequest()->getHttpHost(). Hosts with port are not covered
            $this->_oauthService->setRequestUrl(
                $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost() .
                $this->getRequest()->getRequestUri()
            );

            $this->_oauthService->setRequestMethod($this->getRequest()->getMethod());

            //Combine request and header parameters
            $accessTokenData = array_merge($this->_params, $this->_protocolParams);

            //Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken($accessTokenData);

        } catch (Exception $exception) {
            $response = $this->reportProblem(
                $this->_oauthService->getErrorMap(),
                $this->_oauthService->getErrorToHttpCodeMap(),
                $exception
            );
        }
        $this->getResponse()->setBody($response);
    }

}
