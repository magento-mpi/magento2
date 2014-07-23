<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option;

interface WriteServiceInterface
{
    /**
     * Remove bundle option
     *
     * @param string $productSku
     * @param int $optionId
     * @return bool
     */
    public function remove($productSku, $optionId);

    /**
     * @param string $productSku
     * @param \Magento\Bundle\Service\V1\Data\Product\Option $option
     * @return \Magento\Bundle\Service\V1\Data\Product\Option
     */
    public function add($productSku, \Magento\Bundle\Service\V1\Data\Product\Option $option);
}
