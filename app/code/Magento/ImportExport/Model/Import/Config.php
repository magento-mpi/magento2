<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Import_Config
    extends Magento_Config_Data
    implements Magento_ImportExport_Model_Import_ConfigInterface
{
    /**
     * @param Magento_ImportExport_Model_Import_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_ImportExport_Model_Import_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'import_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve import entities configuration
     *
     * @return array
     */
    function getEntities()
    {
        return $this->get('entities');
    }

    /**
     * Retrieve import product types configuration
     *
     * @return array
     */
    function getProductTypes()
    {
        return $this->get('productTypes');
    }
}
