<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Options;

use Magento\ConfigurableProduct\Service\V1\Data\Option;

interface WriteServiceInterface
{
    /**
     * Add option to the product
     *
     * @param string $productSku
     * @param \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     * @throw \Magento\Framework\Exception\NoSuchEntityException|\Magento\Framework\Exception\CouldNotSaveException|\DomainException
     * @return \Magento\ConfigurableProduct\Service\V1\Data\Option $option
     */
    public function add($productSku, Option $option);
}
