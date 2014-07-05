<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Catalog\Service\V1\Product\CustomOptions;

interface ReadServiceInterface
{
    /**
     * Get custom option types
     *
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionType[]
     */
    public function getTypes();

    /**
     * Get the list of custom options for a specific product
     *
     * @param string $productSku
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku);

    /**
     * Get custom option for a specific product
     *
     * @param string $productSku
     * @param string $optionId
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($productSku, $optionId);
}
