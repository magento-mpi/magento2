<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Service\V1;

use Magento\Customer\Service\V1\MetadataServiceInterface;

interface RmaMetadataReadInterface extends MetadataServiceInterface
{
    const ATTRIBUTE_SET_ID = 0;

    const ENTITY_TYPE = 'rma_item';

    const DATA_OBJECT_CLASS_NAME = 'Magento\Rma\Service\V1\Data\Item';
}