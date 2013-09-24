<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
interface Magento_Catalog_Model_ProductOptions_ConfigInterface
{
    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getOption($name);

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll();
}
