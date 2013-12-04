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
interface OauthV1Interface
{
    /**
     * Delete the consumer data associated with the integration including its token and nonce
     *
     * @param int $consumerId
     * @return array Consumer data array
     */
    public function deleteConsumer($consumerId);
} 