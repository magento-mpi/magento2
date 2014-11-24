<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Api\Data;

/**
 * Interface LinkInterface
 * @package Magento\Bundle\Api\Data
 * @see \Magento\Bundle\Service\V1\Data\Product\Link
 */
interface LinkInterface
{
    /**
     * Get linked product sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

    /**
     * Get qty
     *
     * @return float|null
     */
    public function getQty();

    /**
     * Get position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get is defined
     *
     * @return bool|null
     */
    public function getIsDefined();

    /**
     * Get is default
     *
     * @return bool
     */
    public function getIsDefault();

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice();

    /**
     * Get price type
     *
     * @return int
     */
    public function getPriceType();

    /**
     * Get whether quantity could be changed
     *
     * @return int|null
     */
    public function getCanChangeQuantity();
}
