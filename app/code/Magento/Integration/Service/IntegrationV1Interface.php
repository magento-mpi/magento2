<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

/**
 * Integration Service Interface
 */
interface IntegrationV1Interface
{
    /**
     * Create a new Integration
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Magento\Integration\Exception
     */
    public function create(array $integrationData);

    /**
     * Get the details of a specific Integration.
     *
     * @param int $integrationId
     * @return array Integration data
     * @throws \Magento\Integration\Exception
     */
    public function get($integrationId);

    /**
     * Find Integration by name.
     *
     * @param string $integrationName
     * @return array|null Integration data or null if not found
     */
    public function findByName($integrationName);

    /**
     * Update an Integration.
     *
     * @param array $integrationData
     * @return array Integration data
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
