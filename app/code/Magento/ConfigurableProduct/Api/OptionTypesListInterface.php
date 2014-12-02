<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\ConfigurableProduct\Api;

interface OptionTypesListInterface
{
    /**
     * Get all available option types for configurable product
     *
     * @return string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Webapi\Exception
     * @see \Magento\ConfigurableProduct\Service\V1\Product\Option\ReadServiceInterface::getTypes
     */
    public function getItems();
}
