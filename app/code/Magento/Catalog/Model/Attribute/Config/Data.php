<?php
/**
 * Represents catalog attributes data for a given scope.
 * Provides an abstraction of where the actual data is coming from: config files in the file system, or cache.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Attribute_Config_Data extends Magento_Config_Data
{
    /**
     * @param Magento_Catalog_Model_Attribute_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     */
    public function __construct(
        Magento_Catalog_Model_Attribute_Config_Reader $reader,
        Magento_Config_CacheInterface $cache
    ) {
        parent::__construct($reader, $cache, 'catalog_attributes');
    }
}
