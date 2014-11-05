<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model\Metadata;

use Magento\Customer\Api\AddressMetadataInterface;

/**
 * Cached customer address attribute metadata
 */
class AddressCachedMetadata extends CachedMetadata implements AddressMetadataInterface
{
    /**
     * Initialize dependencies.
     *
     * @param AddressMetadata $metadata
     */
    public function __construct(AddressMetadata $metadata)
    {
        $this->metadata = $metadata;
    }
}
