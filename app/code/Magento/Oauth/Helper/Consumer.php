<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Oauth\Helper;

use \Magento\Oauth\OauthInterface;

class Consumer
{
    /** @var  \Magento\Core\Model\StoreManagerInterface */
    protected $_storeManager;

    /** @var  \Magento\Oauth\Model\Consumer\Factory */
    protected $_consumerFactory;

    /** @var  \Magento\Oauth\Model\Token\Factory */
    protected $_tokenFactory;

    /** @var  \Magento\Oauth\Helper\Data */
    protected $_dataHelper;

    /** @var  \Magento\HTTP\ZendClient */
    protected $_httpClient;

    /**
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Oauth\Model\Consumer\Factory $consumerFactory
     * @param \Magento\Oauth\Model\Token\Factory $tokenFactory
     * @param \Magento\Oauth\Helper\Data $dataHelper
     * @param \Magento\HTTP\ZendClient $httpClient
     */
    public function __construct(
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Oauth\Model\Consumer\Factory $consumerFactory,
        \Magento\Oauth\Model\Token\Factory $tokenFactory,
        \Magento\Oauth\Helper\Data $dataHelper,
        \Magento\HTTP\ZendClient $httpClient
    ) {
        $this->_storeManager = $storeManager;
        $this->_consumerFactory = $consumerFactory;
        $this->_tokenFactory = $tokenFactory;
        $this->_dataHelper = $dataHelper;
        $this->_httpClient = $httpClient;
    }

    /**
     * Create a new consumer account when an integration is installed.
     *
     * @param array $consumerData - Information provided by an integration when the integration is installed.
     * <pre>
     * array(
     *     'name' => 'Integration Name',
     *     'key' => 'a6aa81cc3e65e2960a4879392445e718',
     *     'secret' => 'b7bb92dd4f76f3a71b598a4a3556f829',
     *     'http_post_url' => 'http://www.my-add-on.com'
     * )
     * </pre>
     * @return array - The integration (consumer) data.
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function createConsumer($consumerData)
    {
        try {
            $consumer = $this->_consumerFactory->create($consumerData);
            $consumer->save();
            return $consumer->getData();
        } catch (\Magento\Core\Exception $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('Unexpected error. Unable to create OAuth Consumer account.'));
        }
    }

    /**
     * Execute post to integration (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param int $consumerId - The consumer Id.
     * @return string - The oauth_verifier.
     * @throws \Magento\Core\Exception
     * @throws \Magento\Oauth\Exception
     */
    public function postToConsumer($consumerId)
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
            $this->_httpClient->setUri($consumerData['http_post_url']);
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
        } catch (\Exception $exception) {
            throw new \Magento\Oauth\Exception(__('Unable to post data to consumer due to an unexpected error'));
        }
    }
}
