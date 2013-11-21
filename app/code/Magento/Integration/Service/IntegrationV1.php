<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationInterface;
use Magento\Integration\Model\Integration\Factory as IntegrationFactory;
use Magento\Authz\Model\UserIdentifier\Factory as UserIdentifierFactory;
use Magento\Integration\Model\Integration as IntegrationModel;

/**
 * Integration Service.
 *
 * This service is used to interact with integrations.
 */
class IntegrationV1 implements \Magento\Integration\Service\IntegrationV1Interface
{
    /** @var IntegrationFactory */
    protected $_integrationFactory;

    /** @var AuthorizationInterface */
    protected $_authzService;

    /** @var UserIdentifierFactory */
    protected $_userIdentifierFactory;

    /**
     * Construct and initialize Integration Factory
     *
     * @param IntegrationFactory $integrationFactory
     * @param AuthorizationInterface $authzService
     * @param UserIdentifierFactory $userIdentifierFactory
     */
    public function __construct(
        IntegrationFactory $integrationFactory,
        AuthorizationInterface $authzService,
        UserIdentifierFactory $userIdentifierFactory
    ) {
        $this->_integrationFactory = $integrationFactory;
        $this->_authzService = $authzService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
    }

    /**
     * Create a new Integration
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Magento\Integration\Exception
     */
    public function create(array $integrationData)
    {
        $this->_checkIntegrationByName($integrationData['name']);
        $integration = $this->_integrationFactory->create($integrationData);
        $integration->save();
        $this->_saveApiPermissions($integration);
        return $integration->getData();
    }

    /**
     * Update an Integration.
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Magento\Integration\Exception
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
        $this->_saveApiPermissions($integration);
        return $integration->getData();
    }

    /**
     * Get the details of a specific Integration.
     *
     * @param int $integrationId
     * @return array Integration data
     * @throws \Magento\Integration\Exception
     */
    public function get($integrationId)
    {
        $integration = $this->_loadIntegrationById($integrationId);
        $this->_addAllowedResources($integration);
        return $integration->getData();
    }

    /**
     * Check if an integration exists by the name
     *
     * @param string $name
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
            throw new \Magento\Integration\Exception(__("Integration with ID '%1' doesn't exist.", $integrationId));
        }
        return $integration;
    }

    /**
     * Persist API permissions.
     *
     * Permissions are expected to be set to integration object by 'resource' key.
     * If 'all_resources' is set and is evaluated to true, permissions to all resources will be granted.
     *
     * @param IntegrationModel $integration
     */
    protected function _saveApiPermissions(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            if ($integration->getData('all_resources')) {
                $this->_authzService->grantAllPermissions($userIdentifier);
            } else if (is_array($integration->getData('resource'))) {
                $this->_authzService->grantPermissions($userIdentifier, $integration->getData('resource'));
            }
        }
    }

    /**
     * Add the list of allowed resources to the integration object data by 'resource' key.
     *
     * @param IntegrationModel $integration
     */
    protected function _addAllowedResources(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            $integration->setData('resource', $this->_authzService->getAllowedResources($userIdentifier));
        }
    }

    /**
     * Instantiate new user identifier for an integration.
     *
     * @param int $integrationId
     * @return UserIdentifier
     */
    protected function _createUserIdentifier($integrationId)
    {
        $userIdentifier = $this->_userIdentifierFactory->create(
            UserIdentifier::USER_TYPE_INTEGRATION,
            (int)$integrationId
        );
        return $userIdentifier;
    }
}
