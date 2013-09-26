<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\ProductTypes;

interface ConfigInterface
{
    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getType($name);

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll();
}
