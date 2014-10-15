<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductLinkRepositoryInterface
{
    /**
     * Provide the list of links for a specific product
     *
     * @param string $productSku
     * @param string $type
     * @return Data\ProductLinkInterface[]
     */
    public function getList($productSku, $type);

    /**
     * Save product link
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct
     * @return bool
     * @see \Magento\Catalog\Service\V1\Product\Link\WriteServiceInterface::update - prevuois implementation
     */
    public function save(\Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct);

    /**
     * Save product link collection
     *
     * @param \Magento\Catalog\Api\Data\ProductLinkInterface[] $linkedProducts
     * @return bool
     */
    public function assign(array $linkedProducts);

    /**
     * Delete product link
     *
     * @param Data\ProductLinkInterface $linkedProduct
     * @return bool
     */
    public function delete(\Magento\Catalog\Api\Data\ProductLinkInterface $linkedProduct);

    /**
     * Delete product link by identifier
     *
     * @param string $productSku
     * @param string $linkType
     * @param string $linkedProductSku
     * @return bool
     */
    public function deleteByIdentifier($productSku, $linkType, $linkedProductSku);
}
