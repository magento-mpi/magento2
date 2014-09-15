<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\CustomOptions;

interface WriteServiceInterface
{
    /**
     * Remove custom option from product
     *
     * @param string $productSku
     * @param int $optionId
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException
     * @return bool
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
     */
    public function update(
        $productSku,
        $optionId,
        \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
    );
}
