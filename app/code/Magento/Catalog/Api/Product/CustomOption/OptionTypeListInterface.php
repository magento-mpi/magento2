<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Api\Product\CustomOption;

/**
 * @todo Implementation \Magento\Catalog\Model\ProductOptions\Config
 */
interface OptionTypeListInterface
{
    /**
     * Get custom option types
     *
     * @return \Magento\Catalog\Api\Data\Product\CustomOption\OptionTypeInterface[]
     * @see \Magento\Catalog\Service\V1\Product\CustomOptions\ReadServiceInterface::getTypes - previous implementation
     */
    public function getItems();
}
