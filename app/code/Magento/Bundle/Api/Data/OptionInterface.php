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
 * Interface OptionInterface
 * @package Magento\Bundle\Api\Data
 * @see \Magento\Bundle\Service\V1\Data\Product\Option
 */
interface OptionInterface
{
    /**
     * Get option id
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get option title
     *
     * @return string|null
     */
    public function getTitle();

    /**
     * Get is required option
     *
     * @return bool|null
     */
    public function getIsRequired();

    /**
     * Get input type
     *
     * @return string|null
     */
    public function getType();

    /**
     * Get option position
     *
     * @return int|null
     */
    public function getPosition();

    /**
     * Get product sku
     *
     * @return string|null
     */
    public function getSku();

    /**
     * Get product links
     *
     * @return \Magento\Bundle\Api\Data\LinkInterface[]|null
     */
    public function getProductLinks();
}
