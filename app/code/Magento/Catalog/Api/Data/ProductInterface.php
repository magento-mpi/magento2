<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

/**
 * ProductInterface will be implemented by \Magento\Catalog\Model\Product
 * @see \Magento\Catalog\Service\V1\Data\Product
 */
interface ProductInterface
{
    /**
     * Product sku
     *
     * @return string
     */
    public function getSku();

    /**
     * Product name
     *
     * @return string|null
     */
    public function getName();

    /**
     * Product store id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Product attribute set id
     *
     * @return int|null
     */
    public function getAttributeSetId();

    /**
     * Product price
     *
     * @return float|null
     */
    public function getPrice();

    /**
     * Product status
     *
     * @return int|null
     */
    public function getStatus();

    /**
     * Product visibility
     *
     * @return int|null
     */
    public function getVisibility();

    /**
     * Product type id
     *
     * @return string|null
     */
    public function getTypeId();

    /**
     * Product created date
     *
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Product updated date
     *
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Product weight
     *
     * @return float|null
     */
    public function getWeight();
}
