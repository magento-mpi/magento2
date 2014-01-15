<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Service;

use \Magento\Integration\Model\Integration as IntegrationModel;

/**
 * Integration Service Interface
 */
interface IntegrationV1Interface
{
    /**
     * Create a new Integration
     *
     * @param array $integrationData
     * @return IntegrationModel
     * @throws \Magento\Integration\Exception
     */
    public function create(array $integrationData);

    /**
     * Get the details of a specific Integration.
     *
     * @param int $integrationId
     * @return IntegrationModel
     * @throws \Magento\Integration\Exception
     */
    public function get($integrationId);

    /**
     * Find Integration by name.
     *
     * @param string $integrationName
     * @return IntegrationModel
     */
    public function findByName($integrationName);

    /**
     * Get the details of an Integration by consumer_id.
     *
     * @param int $consumerId
     * @return IntegrationModel
     */
    public function findByConsumerId($consumerId);

    /**
     * Update an Integration.
     *
     * @param array $integrationData
     * @return IntegrationModel
     * @throws \Magento\Integration\Exception
     */
    public function update(array $integrationData);

    /**
     * Delete an Integration.
     *
     * @param int $integrationId
     * @return array Integration data
     * @throws \Magento\Integration\Exception if the integration does not exist or cannot be deleted
     */
    public function delete($integrationId);
}
