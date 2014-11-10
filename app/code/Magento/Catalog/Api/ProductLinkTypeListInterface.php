<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductLinkTypeListInterface
{
    /**
     * Retrieve information about available product link types
     *
     * @return \Magento\Catalog\Api\Data\ProductLinkTypeInterface[]
     */
    public function getItems();

    /**
     * Provide a list of the product link type attributes
     *
     * @param string $type
     * @return \Magento\Catalog\Api\Data\ProductLinkAttributeInterface[]
     */
    public function getItemAttributes($type);
}
