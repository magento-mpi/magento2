<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

use Magento\Integration\Model\Oauth\Token;

/**
 * Integration oAuth Service Interface
 */
interface OauthV1Interface
{
    /**
     * Create a new consumer account.
     *
     * @param array $consumerData - Information provided by an integration when the integration is installed.
     * <pre>
     * array(
     *     'name' => 'Integration Name',
     *     '...' => '...', // Other consumer data can be passed as well
     * )
     * </pre>
     * @return \Magento\Integration\Model\Oauth\Consumer
     * @throws \Magento\Framework\Model\Exception
     * @throws \Magento\Framework\Oauth\Exception
     */
    public function createConsumer($consumerData);

    /**
     * Create access token for provided consumer.
     *
     * @param int $consumerId
     * @param bool $clearExistingToken
     * @return bool If token was created
     */
    public function createAccessToken($consumerId, $clearExistingToken = false);

    /**
     * Retrieve access token assigned to the consumer.
     *
     * @param int $consumerId
     * @return Token|bool Return false if no access token is available.
     */
    public function getAccessToken($consumerId);

    /**
     * Load consumer by its ID.
     *
     * @param int $consumerId
     * @return \Magento\Integration\Model\Oauth\Consumer
     * @throws \Magento\Framework\Oauth\Exception
     * @throws \Magento\Framework\Model\Exception
     */
    public function loadConsumer($consumerId);

    /**
     * Load consumer by its key.
     *
     * @param string $key
     * @return \Magento\Integration\Model\Oauth\Consumer
     * @throws \Magento\Framework\Oauth\Exception
     * @throws \Magento\Framework\Model\Exception
     */
    public function loadConsumerByKey($key);

    /**
     * Execute post to integration (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param int $consumerId - The consumer Id.
     * @param string $endpointUrl - The integration endpoint Url (for HTTP Post)
     * @return string - The oauth_verifier.
     * @throws \Magento\Framework\Model\Exception
     * @throws \Magento\Framework\Oauth\Exception
     */
    public function postToConsumer($consumerId, $endpointUrl);

    /**
     * Delete the consumer data associated with the integration including its token and nonce
     *
     * @param int $consumerId
     * @return array Consumer data array
     */
    public function deleteConsumer($consumerId);

    /**
     * Remove token associated with provided consumer.
     *
     * @param int $consumerId
     * @return bool If token was deleted
     */
    public function deleteToken($consumerId);
}
