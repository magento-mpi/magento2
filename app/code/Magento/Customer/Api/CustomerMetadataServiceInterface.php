<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api;

/**
 * Interface for retrieval information about customer attributes metadata.
 */
interface CustomerMetadataServiceInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_CUSTOMER = 1;

    const ENTITY_TYPE_CUSTOMER = 'customer';

    const DATA_INTERFACE_NAME = 'Magento\Customer\Api\Data\Customer';
}
