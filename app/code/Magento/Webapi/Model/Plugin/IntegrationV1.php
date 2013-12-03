<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;
use Magento\Integration\Model\Integration;

/**
 * Plugin for Magento\Integration\Service\IntegrationV1 service to delete resources associated with the integration
 */
class IntegrationV1
{
    /**
     * Authorization service
     *
     * @var \Magento\Authz\Service\AuthorizationV1
     */
    protected $_authzService;

    /**
     * Factory to create UserIdentifier
     *
     * @var \Magento\Authz\Model\UserIdentifier\Factory
     */
    protected $_userIdentifierFactory;

    /** @var \Magento\Logger */
    protected $_logger;

    /**
     * Construct IntegrationV1 plugin instance
     *
     * @param \Magento\Authz\Service\AuthorizationV1 $authzService
     * @param \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        \Magento\Authz\Service\AuthorizationV1 $authzService,
        \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory,
        \Magento\Logger $logger
    ) {
        $this->_authzService = $authzService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
        $this->_logger = $logger;
    }

    /**
     * Process integration resource permissions after the integration is created
     *
     * @param array $integrationData Data of integration deleted
     * @return array $integrationData
     */
    public function afterDelete(array $integrationData)
    {
        //No check needed for integration data since it cannot be empty in the parent invocation - delete
        $userIdentifier = $this->_userIdentifierFactory->create(
            UserIdentifier::USER_TYPE_INTEGRATION,
            (int)$integrationData[Integration::ID]
        );
        $this->_authzService->removeRoleAndPermissions($userIdentifier);
        return $integrationData;
    }
}