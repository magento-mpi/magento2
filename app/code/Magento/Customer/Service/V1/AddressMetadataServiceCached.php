<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Cached customer address attribute metadata service
 */
class AddressMetadataServiceCached extends MetadataServiceCached implements AddressMetadataServiceInterface
{
    /**
     * Initialize dependencies.
     *
     * @param AddressMetadataService $metadataService
     */
    public function __construct(AddressMetadataService $metadataService)
    {
        $this->metadataService = $metadataService;
    }
}
