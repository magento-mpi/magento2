<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

interface ProductCustomOptionRepositoryInterface
{
    /**
     * Get the list of custom options for a specific product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface[]
     */
    public function getList($productSku);

    /**
     * Get custom option for a specific product
     *
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     */
    public function get($productSku, $optionId);

    /**
     * Delete custom option from product
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return bool
     */
    public function delete(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option);

    /**
     * Save custom option
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     */
    public function save(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option);

    /**
     * @param string $productSku
     * @param int $optionId
     * @return mixed
     */
    public function deleteByIdentifier($productSku, $optionId);
}
