<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Option\Metadata;

interface ConverterInterface
{
    /**
     * Convert option data object value to array representation
     *
     * @param \Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option
     * @return array
     */
    public function convert(\Magento\Catalog\Api\Data\ProductCustomOptionOptionInterface $option);
}
