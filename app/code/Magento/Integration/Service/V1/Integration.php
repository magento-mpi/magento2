<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Integration\Service\V1;

use Magento\Integration\Model\Integration as IntegrationModel;
use Magento\Integration\Model\Integration\Factory as IntegrationFactory;
use Magento\Integration\Service\V1\OauthInterface as IntegrationOauthService;

/**
 * Integration Service.
 *
 * This service is used to interact with integrations.
 */
class Integration implements \Magento\Integration\Service\V1\IntegrationInterface
{
    /**
     * @var IntegrationFactory
     */
    protected $_integrationFactory;

    /**
     * @var IntegrationOauthService
     */
    protected $_oauthService;

    /**
     * Construct and initialize Integration Factory
     *
     * @param IntegrationFactory $integrationFactory
     * @param IntegrationOauthService $oauthService
     */
    public function __construct(IntegrationFactory $integrationFactory, IntegrationOauthService $oauthService)
    {
        $this->_integrationFactory = $integrationFactory;
        $this->_oauthService = $oauthService;
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $integrationData)
    {
        $this->_checkIntegrationByName($integrationData['name']);
        $integration = $this->_integrationFactory->create($integrationData);
        // TODO: Think about double save issue
        $integration->save();
        $consumerName = 'Integration' . $integration->getId();
        $consumer = $this->_oauthService->createConsumer(['name' => $consumerName]);
        $integration->setConsumerId($consumer->getId());
        $integration->save();
        return $integration;
    }

    /**
     * {@inheritdoc}
     */
    public function update(array $integrationData)
    {
        $integration = $this->_loadIntegrationById($integrationData['integration_id']);
        //If name has been updated check if it conflicts with an existing integration
        if ($integration->getName() != $integrationData['name']) {
            $this->_checkIntegrationByName($integrationData['name']);
        }
        $integration->addData($integrationData);
        $integration->save();
        return $integration;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($integrationId)
    {
        $integration = $this->_loadIntegrationById($integrationId);
        $data = $integration->getData();
        $integration->delete();
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function get($integrationId)
    {
        $integration = $this->_loadIntegrationById($integrationId);
        $this->_addOauthConsumerData($integration);
        $this->_addOauthTokenData($integration);
        return $integration;
    }

    /**
     * {@inheritdoc}
     */
    public function findByName($name)
    {
        $integration = $this->_integrationFactory->create()->load($name, 'name');
        return $integration;
    }

    /**
     * {@inheritdoc}
     */
    public function findByConsumerId($consumerId)
    {
        $integration = $this->_integrationFactory->create()->load($consumerId, 'consumer_id');
        return $integration;
    }

    /**
     * {@inheritdoc}
     */
    public function findActiveIntegrationByConsumerId($consumerId)
    {
        $integration = $this->_integrationFactory->create()->loadActiveIntegrationByConsumerId($consumerId);
        return $integration;
    }

    /**
     * Check if an integration exists by the name
     *
     * @param string $name
     * @return void
     * @throws \Magento\Integration\Exception
     */
    private function _checkIntegrationByName($name)
    {
        $integration = $this->_integrationFactory->create()->load($name, 'name');
        if ($integration->getId()) {
            throw new \Magento\Integration\Exception(__("Integration with name '%1' exists.", $name));
        }
    }

    /**
     * Load integration by id.
     *
     * @param int $integrationId
     * @return IntegrationModel
     * @throws \Magento\Integration\Exception
     */
    protected function _loadIntegrationById($integrationId)
    {
        $integration = $this->_integrationFactory->create()->load($integrationId);
        if (!$integration->getId()) {
            throw new \Magento\Integration\Exception(__("Integration with ID '%1' does not exist.", $integrationId));
        }
        return $integration;
    }

    /**
     * Add oAuth consumer key and secret.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _addOauthConsumerData(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $consumer = $this->_oauthService->loadConsumer($integration->getConsumerId());
            $integration->setData('consumer_key', $consumer->getKey());
            $integration->setData('consumer_secret', $consumer->getSecret());
        }
    }

    /**
     * Add oAuth token and token secret.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _addOauthTokenData(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $accessToken = $this->_oauthService->getAccessToken($integration->getConsumerId());
            if ($accessToken) {
                $integration->setData('token', $accessToken->getToken());
                $integration->setData('token_secret', $accessToken->getSecret());
            }
        }
    }
}
