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
 * Plugin for Magento\Core\Model\Resource\Setup model to manage resource permissions of
 * integration installed from config file
 */
class Setup
{
    /**
     * API Integration config
     *
     * @var Config
     */
    protected $_integrationConfig;

    /**
     * Integration service
     *
     * @var \Magento\Integration\Service\IntegrationV1Interface
     */
    protected $_integrationService;

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


    /**
     * Construct Setup plugin instance
     *
     * @param \Magento\Webapi\Model\IntegrationConfig $integrationConfig
     * @param \Magento\Authz\Service\AuthorizationV1 $authzService
     * @param \Magento\Integration\Service\IntegrationV1Interface $integrationService
     * @param \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory
     */
    public function __construct(
        \Magento\Webapi\Model\IntegrationConfig $integrationConfig,
        \Magento\Authz\Service\AuthorizationV1 $authzService,
        \Magento\Integration\Service\IntegrationV1Interface $integrationService,
        \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory
    ) {
        $this->_integrationConfig = $integrationConfig;
        $this->_authzService = $authzService;
        $this->_integrationService = $integrationService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
    }

    /**
     * Process integration resource permissions after the integration is created
     *
     * @param array $integrationNames Name of integrations passed as array from the invocation chain
     * @return array
     */
    public function afterInitIntegrationProcessing($integrationNames)
    {
        if (empty($integrationNames)) {
            return array();
        }
        /** @var array $integrations */
        $integrations = $this->_integrationConfig->getIntegrations();
        foreach ($integrationNames as $name) {
            if (isset($integrations[$name])) {
                $integration = $this->_integrationService->findByName($name);
                if ($integration->getId()) {
                    $userIdentifier = $this->_userIdentifierFactory->create(
                        UserIdentifier::USER_TYPE_INTEGRATION,
                        $integration->getId()
                    );
                    $this->_authzService->grantPermissions(
                        $userIdentifier,
                        $integrations[$name]['resources']
                    );
                }
            }
        }
        return $integrationNames;
    }
}
