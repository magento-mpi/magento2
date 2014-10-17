<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Webapi\Model;

use Magento\Webapi\Model\Cache\Type;
use Magento\Webapi\Model\Config\Reader;
use Zend\Code\Reflection\ClassReflection;

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
    const SERVICE_CLASS_PATTERN = '/^(.+?)\\\\(.+?)\\\\Service\\\\(V\d+)+(\\\\.+)Interface$/';

    /**
     * @var Processor
     */
    protected $cacheProcessor;

    /**
     * @var Reader
     */
    protected $configReader;

    /**
     * @var array
     */
    protected $services;

    /**
     * @param Processor $cacheProcessor
     * @param Reader $configReader
     */
    public function __construct(Processor $cacheProcessor, Reader $configReader)
    {
        $this->cacheProcessor = $cacheProcessor;
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
            $services = $this->cacheProcessor->loadFromCache(self::CACHE_ID);
            if ($services && is_string($services)) {
                $this->services = unserialize($services);
            } else {
                $this->services = $this->configReader->read();
                $this->cacheProcessor->saveToCache(serialize($this->services), self::CACHE_ID);
            }
        }
        return $this->services;
    }
}
