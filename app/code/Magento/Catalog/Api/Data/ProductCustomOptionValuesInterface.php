<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Data;

interface ProductCustomOptionValuesInterface
{
    /**
     * Get option title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder();


    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get price type
     *
     * @return string
     */
    public function getPriceType();

    /**
     * Get Sku
     *
     * @return string|null
     */
    public function getSku();


    /**
     * Get Option type id
     *
     * @return int|null
     */
    public function getOptionTypeId();
}
