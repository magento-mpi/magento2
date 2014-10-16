<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductLinkManagementInterface
{
    /**
     * Provide the list of links for a specific product
     *
     * @param string $productSku
     * @param string $linkType
     * @return Data\ProductLinkInterface[]
     */
    public function getLinkedItemsByType($productSku, $linkType);

    /**
     * Assign a product link to another product
     *
     * @param string $productSku
     * @param string $linkType
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     */
    public function setProductLinks($productSku, $linkType, array $items);
}
