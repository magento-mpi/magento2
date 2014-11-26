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
interface OptionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Get option id
     *
     * @return int|null
     */
    public function getOptionId();

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
    public function getRequired();

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
