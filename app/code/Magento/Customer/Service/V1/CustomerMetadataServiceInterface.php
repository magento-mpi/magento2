<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Interface CustomerMetadataServiceInterface
 */
interface CustomerMetadataServiceInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID_CUSTOMER = 1;

    const ENTITY_TYPE_CUSTOMER = 'customer';

    const DATA_OBJECT_CLASS_NAME = 'Magento\Customer\Service\V1\Data\Customer';
}
