<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Integration\Controller;

use Magento\Integration\Service\OauthV1Interface as IntegrationOauthService;
use Magento\Integration\Service\IntegrationV1Interface as IntegrationService;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * oAuth token controller
 */
class Token extends \Magento\App\Action\Action
{
    /** @var  \Magento\Oauth\OauthInterface */
    protected $_oauthService;

    /** @var  IntegrationOauthService */
    protected $_intOauthService;

    /** @var  IntegrationService */
    protected $_integrationService;

    /** @var  \Magento\Oauth\Helper\Request */
    protected $_helper;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Oauth\OauthInterface $oauthService
     * @param IntegrationOauthService $intOauthService
     * @param IntegrationService $integrationService
     * @param \Magento\Oauth\Helper\Request $helper
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Oauth\OauthInterface $oauthService,
        IntegrationOauthService $intOauthService,
        IntegrationService $integrationService,
        \Magento\Oauth\Helper\Request $helper
    ) {
        parent::__construct($context);
        $this->_oauthService = $oauthService;
        $this->_intOauthService = $intOauthService;
        $this->_integrationService = $integrationService;
        $this->_helper = $helper;
    }

    /**
     *  Initiate RequestToken request operation
     */
    public function requestAction()
    {
        try {
            $requestUrl = $this->_helper->getRequestUrl($this->getRequest());
            $request = $this->_helper->prepareRequest($this->getRequest(), $requestUrl);

            // Request request token
            $response = $this->_oauthService->getRequestToken(
                $request, $requestUrl, $this->getRequest()->getMethod());
        } catch (\Exception $exception) {
            $response = $this->_helper->prepareErrorResponse(
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
            $requestUrl = $this->_helper->getRequestUrl($this->getRequest());
            $request = $this->_helper->prepareRequest($this->getRequest(), $requestUrl);

            // Request access token in exchange of a pre-authorized token
            $response = $this->_oauthService->getAccessToken(
                $request,
                $requestUrl,
                $this->getRequest()->getMethod()
            );
            //After sending the access token, update the integration status to active;
            $consumer = $this->_intOauthService->loadConsumerByKey($request['oauth_consumer_key']);
            $this->_integrationService->findByConsumerId($consumer->getId())
                ->setStatus(IntegrationModel::STATUS_ACTIVE)
                ->save();
        } catch (\Exception $exception) {
            $response = $this->_helper->prepareErrorResponse(
                $exception,
                $this->getResponse()
            );
        }
        $this->getResponse()->setBody(http_build_query($response));
    }
}
