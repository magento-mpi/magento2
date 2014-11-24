<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Api;

interface ProductOptionTypeListInterface
{
    /**
     * Get all types for options for bundle products
     *
     * @return \Magento\Bundle\Api\Data\OptionTypeInterface[]
     * @see \Magento\Bundle\Service\V1\Product\Option\Type\ReadServiceInterface::getTypes
     */
    public function getItems();
}
