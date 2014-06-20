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

interface WriteServiceInterface
{
    /**
     * Add custom option value to the existing product option
     *
     * @param string $productSku
     * @param int $optionId
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue $value
     * @return bool
     */
    public function add(
        $productSku,
        $optionId,
        \Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue $value
    );
}
