<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;
use Magento\Authz\Model\UserIdentifier\Factory as UserIdentifierFactory;
use Magento\Integration\Model\Integration as IntegrationModel;
use Magento\Authz\Service\AuthorizationV1Interface as AuthorizationInterface;
use Magento\Integration\Service\V1\AuthorizationServiceInterface as IntegrationAuthorizationInterface;
use Magento\Authorization\Model\Acl\AclRetriever;

/**
 * Plugin for \Magento\Integration\Service\V1\Integration.
 */
class IntegrationServiceV1
{
    /** @var AuthorizationInterface */
    protected $_authzService;

    /** @var IntegrationAuthorizationInterface */
    protected $integrationAuthorizationService;

    /** @var UserIdentifierFactory */
    protected $_userIdentifierFactory;

    /** @var  AclRetriever */
    protected $aclRetriever;

    /**
     * Initialize dependencies.
     *
     * @param AuthorizationInterface $authzService
     * @param UserIdentifierFactory $userIdentifierFactory
     * @param IntegrationAuthorizationInterface $integrationAuthorizationService
     * @param AclRetriever $aclRetriever
     */
    public function __construct(
        AuthorizationInterface $authzService,
        UserIdentifierFactory $userIdentifierFactory,
        IntegrationAuthorizationInterface $integrationAuthorizationService,
        AclRetriever $aclRetriever
    ) {
        $this->_authzService = $authzService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
        $this->integrationAuthorizationService = $integrationAuthorizationService;
        $this->aclRetriever  = $aclRetriever;
    }

    /**
     * Persist API permissions.
     *
     * @param \Magento\Integration\Service\V1\Integration $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreate(\Magento\Integration\Service\V1\Integration $subject, $integration)
    {
        $this->_saveApiPermissions($integration);
        return $integration;
    }

    /**
     * Persist API permissions.
     *
     * @param \Magento\Integration\Service\V1\Integration $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterUpdate(\Magento\Integration\Service\V1\Integration $subject, $integration)
    {
        $this->_saveApiPermissions($integration);
        return $integration;
    }

    /**
     * Add API permissions to integration data.
     *
     * @param \Magento\Integration\Service\V1\Integration $subject
     * @param IntegrationModel $integration
     *
     * @return IntegrationModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGet(\Magento\Integration\Service\V1\Integration $subject, $integration)
    {
        $this->_addAllowedResources($integration);
        return $integration;
    }

    /**
     * Add the list of allowed resources to the integration object data by 'resource' key.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _addAllowedResources(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            $integration->setData('resource', $this->aclRetriever->getAllowedResourcesByUser($userIdentifier));
        }
    }

    /**
     * Persist API permissions.
     *
     * Permissions are expected to be set to integration object by 'resource' key.
     * If 'all_resources' is set and is evaluated to true, permissions to all resources will be granted.
     *
     * @param IntegrationModel $integration
     * @return void
     */
    protected function _saveApiPermissions(IntegrationModel $integration)
    {
        if ($integration->getId()) {
            $userIdentifier = $this->_createUserIdentifier($integration->getId());
            if ($integration->getData('all_resources')) {
                $this->integrationAuthorizationService->grantAllPermissions($userIdentifier);
            } else if (is_array($integration->getData('resource'))) {
                $this->integrationAuthorizationService
                    ->grantPermissions($userIdentifier, $integration->getData('resource'));
            } else {
                $this->integrationAuthorizationService->grantPermissions($userIdentifier, array());
            }
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

    /**
     * Process integration resource permissions after the integration is created
     *
     * @param \Magento\Integration\Service\V1\Integration $subject
     * @param array $integrationData Data of integration deleted
     *
     * @return array $integrationData
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDelete(\Magento\Integration\Service\V1\Integration $subject, array $integrationData)
    {
        //No check needed for integration data since it cannot be empty in the parent invocation - delete
        $userIdentifier = $this->_userIdentifierFactory->create(
            UserIdentifier::USER_TYPE_INTEGRATION,
            (int)$integrationData[IntegrationModel::ID]
        );
        $this->integrationAuthorizationService->removePermissions($userIdentifier);
        return $integrationData;
    }
}
