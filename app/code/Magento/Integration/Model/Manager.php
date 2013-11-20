<?php
/**
 * Event manager
 * Used to dispatch global events
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Model;

use Magento\Integration\Model\Integration;

/**
 * Class to manage integrations installed from config file
 *
 * @package Magento\Integration\Model
 */
class Manager
{
    /**
     * Integration service
     *
     * @var \Magento\Integration\Service\IntegrationV1Interface
     */
    protected $_integrationService;

    /**
     * Event config
     *
     * @var Config
     */
    protected $_integrationConfig;

    /**
     * @param Config $integrationConfig
     * @param \Magento\Integration\Service\IntegrationV1Interface $integrationService
     */
    public function __construct(
        Config $integrationConfig,
        \Magento\Integration\Service\IntegrationV1Interface $integrationService
    ) {
        $this->_integrationService = $integrationService;
        $this->_integrationConfig = $integrationConfig;
    }

    /**
     * Process integrations from config files
     */
    public function processIntegrationConfig()
    {
        /** @var array $integrations */
        $integrations = $this->_integrationConfig->getIntegrations();
        foreach ($integrations as $integrationDetails) {
            $integrationData = array(Integration::NAME => $integrationDetails[Integration::NAME]);
            if (isset($integrationDetails[Integration::EMAIL])) {
                $integrationData[Integration::EMAIL] = $integrationDetails[Integration::EMAIL];
            }
            if (isset($integrationDetails[Integration::ENDPOINT])) {
                $integrationData[Integration::ENDPOINT] = $integrationDetails[Integration::ENDPOINT];
            }
            $integrationData[Integration::TYPE] = Integration::TYPE_CONFIG;
            $this->_integrationService->create($integrationData);
        }
    }
}