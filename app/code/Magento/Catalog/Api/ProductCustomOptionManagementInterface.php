<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api;

/**
 * Created from:
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface
 * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface
 * @todo Create new model \Magento\Catalog\Model\Product\CustomOptionManagement
 */
interface ProductCustomOptionManagementInterface
{
    /**
     * Get the list of custom options for a specific product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::getList - previous implementation
     */
    public function getList($productSku);

    /**
     * Get custom option for a specific product
     *
     * @param string $productSku
     * @param string $optionId
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::get - previous implementation
     */
    public function get($productSku, $optionId);

    /**
     * Remove custom option from product
     *
     * @param string $productSku
     * @param int $optionId
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface::remove - previous implementation
     */
    public function remove($productSku, $optionId);

    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface::add - previous implementation
     */
    public function add($productSku, \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option);

    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param string $optionId
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     *
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\WriteServiceInterface::update - previous implementation
     */
    public function update(
        $productSku,
        $optionId,
        \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
    );
}
