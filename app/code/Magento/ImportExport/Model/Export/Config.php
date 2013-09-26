<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_ImportExport_Model_Export_Config
    extends Magento_Config_Data
    implements Magento_ImportExport_Model_Export_ConfigInterface
{
    /**
     * @param Magento_ImportExport_Model_Export_Config_Reader $reader
     * @param Magento_Config_CacheInterface $cache
     * @param string $cacheId
     */
    public function __construct(
        Magento_ImportExport_Model_Export_Config_Reader $reader,
        Magento_Config_CacheInterface $cache,
        $cacheId = 'export_config_cache'
    ) {
        parent::__construct($reader, $cache, $cacheId);
    }

    /**
     * Retrieve export entities configuration
     *
     * @return array
     */
    public function getEntities()
    {
        return $this->get('entities');
    }

    /**
     * Retrieve export product types configuration
     *
     * @return array
     */
    public function getProductTypes()
    {
        return $this->get('productTypes');
    }

    /**
     * Retrieve export file formats configuration
     *
     * @return array
     */
    public function getFileFormats()
    {
        return $this->get('fileFormats');
    }
}
