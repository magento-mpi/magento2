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
     * @param int $integrationName
     * @return IntegrationModel If integration cannot be found - empty model will be returned
     * @throws \Magento\Integration\Exception
     */
    public function findByName($integrationName);


    /**
     * Update a Integration.
     *
     * @param array $integrationData
     * @return IntegrationModel
     * @throws \Magento\Integration\Exception
     */
    public function update(array $integrationData);

}
