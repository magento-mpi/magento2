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
use Magento\Webapi\Model\IntegrationConfig;
use Magento\Integration\Service\V1\AuthorizationServiceInterface as IntegrationAuthorizationInterface;

/**
 * Plugin for Magento\Framework\Module\Setup model to manage resource permissions of
 * integration installed from config file
 */
class Setup
{
    /**
     * API Integration config
     *
     * @var IntegrationConfig
     */
    protected $_integrationConfig;

    /**
     * Integration service
     *
     * @var \Magento\Integration\Service\V1\IntegrationInterface
     */
    protected $_integrationService;

    /**
     * @var IntegrationAuthorizationInterface
     */
    protected $integrationAuthorizationService;

    /**
     * Factory to create UserIdentifier
     *
     * @var \Magento\Authz\Model\UserIdentifier\Factory
     */
    protected $_userIdentifierFactory;

    /**
     * Construct Setup plugin instance
     *
     * @param IntegrationConfig $integrationConfig
     * @param IntegrationAuthorizationInterface $integrationAuthorizationService
     * @param \Magento\Integration\Service\V1\IntegrationInterface $integrationService
     * @param \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory
     */
    public function __construct(
        IntegrationConfig $integrationConfig,
        IntegrationAuthorizationInterface $integrationAuthorizationService,
        \Magento\Integration\Service\V1\IntegrationInterface $integrationService,
        \Magento\Authz\Model\UserIdentifier\Factory $userIdentifierFactory
    ) {
        $this->_integrationConfig = $integrationConfig;
        $this->integrationAuthorizationService = $integrationAuthorizationService;
        $this->_integrationService = $integrationService;
        $this->_userIdentifierFactory = $userIdentifierFactory;
    }

    /**
     * Process integration resource permissions after the integration is created
     *
     * @param \Magento\Integration\Model\Resource\Setup $subject
     * @param string[] $integrationNames Name of integrations passed as array from the invocation chain
     *
     * @return string[]
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterInitIntegrationProcessing(
        \Magento\Integration\Model\Resource\Setup $subject,
        $integrationNames
    ) {
        if (empty($integrationNames)) {
            return array();
        }
        /** @var array $integrations */
        $integrations = $this->_integrationConfig->getIntegrations();
        foreach ($integrationNames as $name) {
            if (isset($integrations[$name])) {
                $integration = $this->_integrationService->findByName($name);
                if ($integration->getId()) {
                    $this->integrationAuthorizationService->grantPermissions(
                        $integration->getId(),
                        $integrations[$name]['resources']
                    );
                }
            }
        }
        return $integrationNames;
    }
}
