<?php
/**
 * REST specific API config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webapi_Model_Config_Reader_Rest extends Mage_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'REST';

    /**
     * Construct config reader with REST class reflector.
     *
     * @param Mage_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector
     * @param Mage_Core_Model_Config $appConfig
     * @param Mage_Core_Model_CacheInterface $cache
     * @param Mage_Core_Model_ModuleListInterface $moduleList
     * @param Mage_Core_Model_Cache_StateInterface $cacheState
     */
    public function __construct(
        Mage_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector,
        Mage_Core_Model_Config $appConfig,
        Mage_Core_Model_CacheInterface $cache,
        Mage_Core_Model_ModuleListInterface $moduleList,
        Mage_Core_Model_Cache_StateInterface $cacheState
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
