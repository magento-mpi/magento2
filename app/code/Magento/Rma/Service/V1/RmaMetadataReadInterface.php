<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Rma\Service\V1;

/**
 * Interface RmaMetadataReadInterface
 */
interface RmaMetadataReadInterface extends \Magento\Customer\Api\MetadataInterface
{
    const ATTRIBUTE_SET_ID = 9;

    const ENTITY_TYPE = 'rma_item';

    const DATA_OBJECT_CLASS_NAME = 'Magento\Rma\Service\V1\Data\Item';
}
