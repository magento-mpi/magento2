<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\CustomOptions;

/**
 * @deprecated
 * @todo remove this interface
 * @see \Magento\Catalog\Api\Product\CustomOptionManagementInterface
 */
interface WriteServiceInterface
{
    /**
     * Remove custom option from product
     *
     * @param string $productSku
     * @param int $optionId
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException
     * @return bool
     * @deprecated
     * @see \Magento\Catalog\Api\Product\CustomOptionManagementInterface::remove
     */
    public function remove($productSku, $optionId);

    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @deprecated
     * @see \Magento\Catalog\Api\Product\CustomOptionManagementInterface::add
     */
    public function add($productSku, \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option);

    /**
     * Add custom option to the product
     *
     * @param string $productSku
     * @param string $optionId
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @deprecated
     * @see \Magento\Catalog\Api\Product\CustomOptionManagementInterface::update
     */
    public function update(
        $productSku,
        $optionId,
        \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
    );
}
