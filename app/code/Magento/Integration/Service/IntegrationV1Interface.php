<?php
/**
 * Integration Service - Version 1.
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

interface IntegrationV1Interface
{

    /**
     * Create a new Integration
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function create(array $integrationData);

    /**
     * Get the details of a specific Integration.
     *
     * @param int $integrationId
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function get($integrationId);


    /**
     * Update a Integration.
     *
     * @param array $integrationData
     * @return array Integration data
     * @throws \Exception|\Magento\Core\Exception
     * @throws \Magento\Integration\Exception
     */
    public function update(array $integrationData);

}
