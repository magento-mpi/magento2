<?php
/**
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
     * Integration config
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
     * Process integrations from config files for the given array of integration names
     *
     * @param array $integrationNames
     */
    public function processIntegrationConfig(array $integrationNames)
    {
        if (empty($integrationNames)) {
            return;
        }
        /** @var array $integrations */
        $integrations = $this->_integrationConfig->getIntegrations();
        foreach ($integrationNames as $name) {
             $integrationDetails = $integrations[$name];
            $integrationData = array(Integration::NAME => $name);
            if (isset($integrationDetails[Integration::EMAIL])) {
                $integrationData[Integration::EMAIL] = $integrationDetails[Integration::EMAIL];
            }
            if (isset($integrationDetails[Integration::ENDPOINT])) {
                $integrationData[Integration::ENDPOINT] = $integrationDetails[Integration::ENDPOINT];
            }
            $integrationData[Integration::SETUP_TYPE] = Integration::TYPE_CONFIG;
            $this->_integrationService->create($integrationData);
        }
    }
}