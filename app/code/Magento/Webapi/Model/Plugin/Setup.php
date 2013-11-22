<?php
/**
 * Plugin for Magento\Core\Model\Resource\Setup model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model\Plugin;

use Magento\Authz\Model\UserIdentifier;

/**
 * Class to manage Api config of integration installed from the api.xml config file
 */
class Setup
{
    /**
     * Integration config
     *
     * @var Config
     */
    protected $_integrationConfig;

    /**
     * Integration service
     *
     * @var \Magento\Authz\Service\AuthorizationV1
     */
    protected $_authzService;


    /**
     * Construct Integration Api processing Setup instance
     *
     * @param \Magento\Webapi\Model\IntegrationConfig $integrationConfig
     * @param \Magento\Authz\Service\AuthorizationV1 $authzService
     */
    public function __construct(
        \Magento\Webapi\Model\IntegrationConfig $integrationConfig,
        \Magento\Authz\Service\AuthorizationV1 $authzService
    ) {
        $this->_integrationConfig = $integrationConfig;
        $this->_authzService = $authzService;
    }

    /**
     * Process integrations from config files
     *
     * @param array $integrationNames Name of integrations passed as array from the invocation chain
     */
    public function afterInitIntegrationProcessing(array $integrationNames)
    {
        /** @var array $integrations */
        $integrations = $this->_integrationConfig->getIntegrations();
        foreach ($integrationNames as $name) {
            $this->_authzService->grantPermissions(
                UserIdentifier::USER_TYPE_INTEGRATION,
                $integrations[$name]['resources']
            );
        }
    }
}