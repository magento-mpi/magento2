<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Config\Reader;
use Zend\Code\Reflection\ClassReflection;
use Magento\Webapi\Model\Cache\Type as WebapiCache;

/**
 * Web API Config Model.
 *
 * This is a parent class for storing information about service configuration.
 */
class Config
{
    const CACHE_ID = 'webapi';

    /**
     * Pattern for Web API interface name.
     */
    const SERVICE_CLASS_PATTERN_DEPRECATED = '/^(.+?)\\\\(.+?)\\\\Service\\\\(V\d+)+(\\\\.+)Interface$/';

    const SERVICE_CLASS_PATTERN = '/^(.+?)\\\\(.+?)\\\\Api(\\\\.+)Interface$/';


    /**
     * @var WebapiCache
     */
    protected $cache;

    /**
     * @var Reader
     */
    protected $configReader;

    /**
     * @var array
     */
    protected $services;

    /**
     * Initialize dependencies.
     *
     * @param WebapiCache $cache
     * @param Reader $configReader
     */
    public function __construct(WebapiCache $cache, Reader $configReader)
    {
        $this->cache = $cache;
        $this->configReader = $configReader;
    }

    /**
     * Return services loaded from cache if enabled or from files merged previously
     *
     * @return array
     */
    public function getServices()
    {
        if (null === $this->services) {
            $services = $this->cache->load(self::CACHE_ID);
            if ($services && is_string($services)) {
                $this->services = unserialize($services);
            } else {
                $this->services = $this->configReader->read();
                $this->cache->save(serialize($this->services), self::CACHE_ID);
            }
        }
        return $this->services;
    }
}
