<?php
/**
 * REST specific API config reader.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webapi_Model_Config_Reader_Rest extends Magento_Webapi_Model_Config_ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'REST';

    /**
     * Construct config reader with REST class reflector.
     *
     * @param Magento_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector
     * @param Magento_Core_Model_Config $appConfig
     * @param Magento_Core_Model_CacheInterface $cache
     */
    public function __construct(
        Magento_Webapi_Model_Config_Reader_Rest_ClassReflector $classReflector,
        Magento_Core_Model_Config $appConfig,
        Magento_Core_Model_CacheInterface $cache
    ) {
        parent::__construct($classReflector, $appConfig, $cache);
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
