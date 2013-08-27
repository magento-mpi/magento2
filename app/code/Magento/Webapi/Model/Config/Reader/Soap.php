<?php
/**
 * SOAP specific API config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Config_Reader_Soap extends Magento_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'SOAP';

    /**
     * Construct config reader with SOAP class reflector.
     *
     * @param Magento_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector
     * @param Magento_Core_Model_Config $appConfig
     * @param Magento_Core_Model_CacheInterface $cache
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Core_Model_Cache_StateInterface $cacheState
     */
    public function __construct(
        Magento_Webapi_Model_Config_Reader_Soap_ClassReflector $classReflector,
        Magento_Core_Model_Config $appConfig,
        Magento_Core_Model_CacheInterface $cache,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Core_Model_Cache_StateInterface $cacheState
    ) {
        parent::__construct($classReflector, $appConfig, $cache, $moduleList, $cacheState);
    }

    /**
     * Retrieve cache ID.
     *
     * @return string
     */
    public function getCacheId()
    {
        return self::CONFIG_CACHE_ID . '-' . self::CONFIG_TYPE;
    }
}
