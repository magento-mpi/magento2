<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Option;

use Magento\ConfigurableProduct\Service\V1\Data\Option;

interface WriteServiceInterface
{
    /**
     * Add option to the product
     *
     * @param string $productSku
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     * @throws \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException|\InvalidArgumentException
     * @return \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     */
    public function add($productSku, Option $option);

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
