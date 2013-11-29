<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Integration\Helper\Oauth;

use Magento\Oauth\OauthInterface;
use Magento\Integration\Model\Oauth\Token\Provider as TokenProvider;
use \Magento\Integration\Model\Oauth\Token as Token;
use \Magento\Integration\Model\Oauth\Token\Factory as TokenFactory;
use \Magento\Integration\Helper\Oauth\Data as IntegrationOauthHelper;
use \Magento\Oauth\Helper\Oauth as OauthHelper;
use \Magento\Integration\Model\Oauth\Consumer\Factory as ConsumerFactory;

// TODO: Fix coupling between objects
class Consumer
{
    /** @var  \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var  ConsumerFactory */
    protected $_consumerFactory;

    /** @var  TokenFactory */
    protected $_tokenFactory;

    /** @var  IntegrationOauthHelper */
    protected $_dataHelper;

    /** @var  \Magento\HTTP\ZendClient */
    protected $_httpClient;

    /** @var \Magento\Logger */
    protected $_logger;

    /** @var OauthHelper */
    protected $_oauthHelper;

    /** @var TokenProvider */
    protected $_tokenProvider;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param ConsumerFactory $consumerFactory
     * @param TokenFactory $tokenFactory
     * @param IntegrationOauthHelper $dataHelper
     * @param \Magento\HTTP\ZendClient $httpClient
     * @param \Magento\Logger $logger
     * @param OauthHelper $oauthHelper
     * @param TokenProvider $tokenProvider
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        ConsumerFactory $consumerFactory,
        TokenFactory $tokenFactory,
        IntegrationOauthHelper $dataHelper,
        \Magento\HTTP\ZendClient $httpClient,
        \Magento\Logger $logger,
        OauthHelper $oauthHelper,
        TokenProvider $tokenProvider
    ) {
        $this->_storeManager = $storeManager;
        $this->_consumerFactory = $consumerFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_dataHelper = $dataHelper;
        $this->_httpClient = $httpClient;
        $this->_logger = $logger;
        $this->_oauthHelper = $oauthHelper;
        $this->_tokenProvider = $tokenProvider;
    }

    /**
     * Create a new consumer account.
     *
     * @param string $consumerName
     * @return \Magento\Integration\Model\Oauth\Consumer
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function createConsumer($consumerName)
    {
        try {
            $consumerData = array(
                'name' => $consumerName,
                'key' => $this->_oauthHelper->generateConsumerKey(),
                'secret' => $this->_oauthHelper->generateConsumerSecret()
            );
            $consumer = $this->_consumerFactory->create($consumerData);
            $consumer->save();
            return $consumer;
        } catch (\Magento\Core\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('Unexpected error. Unable to create oAuth consumer account.'));
        }
    }

    /**
     * Create access token for provided consumer.
     *
     * @param int $consumerId
     */
    public function createAccessToken($consumerId)
    {
        // TODO: This implementation is temporary and should be changed after requirements clarification
        try {
            $existingToken = $this->_consumerFactory->create()->load($consumerId);
        } catch (\Exception $e) {
            return;
        }
        if (!$existingToken) {
            $consumer = $this->_consumerFactory->create()->load($consumerId);
            $this->_tokenFactory->create()->createVerifierToken($consumerId);
            $this->_tokenProvider->createRequestToken($consumer);
            $this->_tokenProvider->getAccessToken($consumer);
        }
    }

    /**
     * Retrieve access token assigned to the consumer.
     *
     * @param int $consumerId
     * @return bool|Token Return false if no access token is available.
     */
    public function getAccessToken($consumerId)
    {
        try {
            $consumer = $this->_consumerFactory->create()->load($consumerId);
            $token = $this->_tokenProvider->getTokenByConsumer($consumer->getId());
            if ($token->getType() != Token::TYPE_ACCESS) {
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
        return $token;
    }

    /**
     * Load consumer by its ID.
     *
     * @param int $consumerId
     * @return \Magento\Integration\Model\Oauth\Consumer
     * @throws \Magento\Oauth\Exception
     * @throws \Exception
     * @throws \Magento\Core\Exception
     */
    public function loadConsumer($consumerId)
    {
        try {
            return $this->_consumerFactory->create()->load($consumerId);
        } catch (\Magento\Core\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('Unexpected error. Unable to load oAuth consumer account.'));
        }
    }

    /**
     * Execute post to integration (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param int $consumerId - The consumer Id.
     * @param string $endpointUrl - The integration endpoint Url (for HTTP Post)
     * @return string - The oauth_verifier.
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function postToConsumer($consumerId, $endpointUrl)
    {
        try {
            $consumer = $this->_consumerFactory->create()->load($consumerId);
            if (!$consumer->getId()) {
                throw new \Magento\Oauth\Exception(
                    __('A consumer with ID %1 does not exist', $consumerId), OauthInterface::ERR_PARAMETER_REJECTED);
            }
            $consumerData = $consumer->getData();
            $verifier = $this->_tokenFactory->create()->createVerifierToken($consumerId);
            $storeBaseUrl = $this->_storeManager->getStore()->getBaseUrl();
            $this->_httpClient->setUri($endpointUrl);
            $this->_httpClient->setParameterPost(
                array(
                    'oauth_consumer_key' => $consumerData['key'],
                    'oauth_consumer_secret' => $consumerData['secret'],
                    'store_base_url' => $storeBaseUrl,
                    'oauth_verifier' => $verifier->getVerifier()
                )
            );
            $maxredirects = $this->_dataHelper->getConsumerPostMaxRedirects();
            $timeout = $this->_dataHelper->getConsumerPostTimeout();
            $this->_httpClient->setConfig(array('maxredirects' => $maxredirects, 'timeout' => $timeout));
            $this->_httpClient->request(\Magento\HTTP\ZendClient::POST);
            return $verifier->getVerifier();
        } catch (\Magento\Core\Exception $exception) {
            throw $exception;
        } catch (\Magento\Oauth\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            $this->_logger->logException($exception);
            throw new \Magento\Oauth\Exception(__('Unable to post data to consumer due to an unexpected error'));
        }
    }
}
