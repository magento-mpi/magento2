<?php
/**
 * Product option value read service
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\OptionValue;

interface ReadServiceInterface
{
    /**
     * Get list of values for specific product option
     *
     * @param string $productSku
     * @param int $optionId
     * @return \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList($productSku, $optionId);
}
