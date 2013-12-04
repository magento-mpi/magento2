<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Integration\Service;

/**
 * Integration Oauth Service Interface
 */
interface IntegrationOauthV1Interface
{
    /**
     * Delete the consumer data associated with the integration including its token and nonce
     *
     * @param $consumerId
     * @return mixed
     */
    public function deleteConsumer($consumerId);
} 