<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Service\V1\Product\Options;

use \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute;

interface WriteServiceInterface
{
    /**
     * Add option to the product
     *
     * @param string $productSku
     * @param \Magento\ConfigurableProduct\Service\V1\Data\ConfigurableAttribute $attribute
     * @throw \Magento\Framework\Exception\NoSuchEntityException
     * @return int Configurable attribute id
     */
    public function add($productSku, ConfigurableAttribute $attribute);
}
