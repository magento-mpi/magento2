<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

interface WriteServiceInterface
{
    /**
     * Add option to the product
     *
     * @param string $productSku
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \InvalidArgumentException
     */
    public function add($productSku, \Magento\ConfigurableProduct\Service\V1\Data\Option $option);

    /**
     * @param string $productSku
     * @param int $optionId
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function update($productSku, $optionId, \Magento\ConfigurableProduct\Service\V1\Data\Option $option);

    /**
     * Remove option from configurable product
     *
     * @param string $productSku
     * @param int $optionId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     */
    public function remove($productSku, $optionId);
}
