<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model\Soap\Config\Reader;

/**
 * SOAP specific API config reader.
 */
class Soap extends \Magento\Webapi\Model\Soap\Config\ReaderAbstract
{
    /**
     * Config type.
     */
    const CONFIG_TYPE = 'SOAP';

    /**
     * Construct config reader.
     *
     * @param \Magento\Webapi\Model\Soap\Config\Reader\Soap\ClassReflector $classReflector
     * @param \Magento\Module\Dir $moduleDir
     * @param \Magento\Webapi\Model\Cache\Type $cache
     * @param \Magento\Module\ModuleListInterface $moduleList
     */
    public function __construct(
        \Magento\Webapi\Model\Soap\Config\Reader\Soap\ClassReflector $classReflector,
        \Magento\Module\Dir $moduleDir,
        \Magento\Webapi\Model\Cache\Type $cache,
        \Magento\Module\ModuleListInterface $moduleList
    ) {
        parent::__construct($classReflector, $moduleDir, $cache, $moduleList);
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
