<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Service\V1\Product\CustomOptions\Data\OptionValue;

interface ConverterInterface
{
    /**
     * Convert option data object value to array representation
     *
     * @param \Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option
     * @return array
     */
    public function convert(\Magento\Catalog\Service\V1\Product\CustomOptions\Data\Option $option);
}
