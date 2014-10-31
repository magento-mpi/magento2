<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Service\V1;

/**
 * Cached customer attribute metadata service
 */
class CustomerMetadataServiceCached extends MetadataServiceCached implements CustomerMetadataServiceInterface
{
    /**
     * Initialize dependencies.
     *
     * @param CustomerMetadataService $metadataService
     */
    public function __construct(CustomerMetadataService $metadataService)
    {
        $this->metadataService = $metadataService;
    }
}
