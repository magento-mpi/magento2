<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Service\V1\Product\Option\Type;

interface ReadServiceInterface
{
    /**
     * Get all types for options for bundle products
     *
     * @return \Magento\Bundle\Service\V1\Data\Product\Option\Type[]
     */
    public function getTypes();
}
