<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Api;

/**
 * Interface for retrieval information about customer address attributes metadata.
 */
interface AddressMetadataInterface extends MetadataInterface
{
    const ATTRIBUTE_SET_ID_ADDRESS = 2;

    const ENTITY_TYPE_ADDRESS = 'customer_address';

    const DATA_INTERFACE_NAME = 'Magento\Customer\Api\Data\Address';

    const DATA_OBJECT_CLASS_NAME = 'Magento\Customer\Api\Data\AddressInterface';
}
