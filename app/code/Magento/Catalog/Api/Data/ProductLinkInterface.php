<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface ProductLinkInterface extends \Magento\Framework\Api\Data\ExtensibleObjectInterface
{
    /**
     * Get product SKU
     *
     * @identifier
     * @return string
     */
    public function getProductSku();

    /**
     * Get link type
     *
     * @identifier
     * @return string
     */
    public function getLinkType();

    /**
     * Get linked product sku
     *
     * @identifier
     * @return string
     */
    public function getLinkedProductSku();

    /**
     * Get linked product type (simple, virtual, etc)
     *
     * @return string
     */
    public function getLinkedProductType();

    /**
     * Get linked item position
     *
     * @return int
     */
    public function getPosition();
}
