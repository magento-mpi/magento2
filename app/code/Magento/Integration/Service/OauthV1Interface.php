<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service;

use \Magento\Integration\Model\Oauth\Token as Token;

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
     */
    public function createConsumer($consumerData);

    /**
     * Create access token for provided consumer.
     *
     * @param int $consumerId
     * @return bool If token was created
     */
    public function createAccessToken($consumerId);

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
     */
    public function loadConsumer($consumerId);

    /**
     * Execute post to integration (consumer) HTTP Post URL. Generate and return oauth_verifier.
     *
     * @param int $consumerId - The consumer Id.
     * @param string $endpointUrl - The integration endpoint Url (for HTTP Post)
     * @return string - The oauth_verifier.
     */
    public function postToConsumer($consumerId, $endpointUrl);
}
