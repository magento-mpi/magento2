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
}
