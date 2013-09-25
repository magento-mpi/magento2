<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_ProductOptions_Config
    extends Magento_Config_Data
    implements Magento_Catalog_Model_ProductOptions_ConfigInterface
{
    /**
     * @param Magento_Catalog_Model_ProductOptions_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_Catalog_Model_ProductOptions_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'product_options_config'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Get configuration of product type by name
     *
     * @param string $name
     * @return array
     */
    public function getOption($name)
    {
        return $this->get($name, array());
    }

    /**
     * Get configuration of all registered product types
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }
}
