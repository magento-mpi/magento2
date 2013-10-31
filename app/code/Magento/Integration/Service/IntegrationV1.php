<?php
/**
 * Integration Service.
 *
 * This service is used to interact with integrations.
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

class IntegrationV1 implements \Magento\Integration\Service\IntegrationV1Interface
{
    /** @var \Magento\Integration\Model\Integration\Factory $_integrationFactory */
    private $_integrationFactory;

    /**
     * @param \Magento\Integration\Model\Integration\Factory $integrationFactory
     */
    public function __construct(
        \Magento\Integration\Model\Integration\Factory $integrationFactory
    ) {
        $this->_integrationFactory = $integrationFactory;
    }

    /**
     * Create a new Integration
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function create(array $integrationData)
    {
        try {
            $integration = $this->_integrationFactory->create($integrationData);
            $this->_validateIntegration($integration);
            $integration->save();
            return $integration->getData();
        } catch (\Magento\Core\Exception $exception) {
            // These messages are already translated, we can simply surface them.
            throw $exception;
        } catch (\Exception $exception) {
            // These messages have no translation, we should not expose our internals but may consider logging them.
            throw new \Magento\Integration\Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Update a Integration.
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function update(array $integrationData)
    {
        try {
            $integration = $this->_loadIntegrationById($integrationData['integration_id']);
            $integration->addData($integrationData);
            $this->_validateIntegration($integration);
            $integration->save();
            return $integration->getData();
        } catch (\Magento\Core\Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Magento\Integration\Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Get the details of a specific Integration.
     *
     * @param int $integrationId
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function get($integrationId)
    {
        try {
            $integration = $this->_loadIntegrationById($integrationId);
            return $integration->getData();
        } catch (\Magento\Core\Exception $e) {
            throw $e;
        } catch (\Exception $e) {
            throw new \Magento\Integration\Exception(
                __('Unexpected error.  Please contact the administrator.')
            );
        }
    }

    /**
     * Validates an integration
     *
     * @param \Magento\Integration\Model\Integration $integration
     * @throws \Magento\Integration\Exception
     */
    private function _validateIntegration(\Magento\Integration\Model\Integration $integration)
    {
        if (!$integration->getName() || !$integration->getEmail() || !$integration->getAuthentication(
            ) || !$integration->getEndpoint()
        ) {
            throw new \Magento\Integration\Exception(
                __('Please enter data for all the required fields.')
            );
        }
    }

    /**
     * Load integration by id.
     *
     * @param int $integrationId
     * @throws \Magento\Integration\Exception
     * @return \Magento\Integration\Model\Integration
     */
    protected function _loadIntegrationById($integrationId)
    {
        $integration = $this->_integrationFactory->create()->load($integrationId);
        if (!$integration->getId()) {
            throw new \Magento\Integration\Exception(
                __("Integration with ID '%1' doesn't exist.", $integrationId)
            );
        }
        return $integration;
    }

}
