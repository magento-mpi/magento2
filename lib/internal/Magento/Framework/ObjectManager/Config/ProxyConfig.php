<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ObjectManager\Config;

use Magento\Framework\ObjectManager\ConfigCache;
use Magento\Framework\ObjectManager\Relations;

class ProxyConfig implements \Magento\Framework\ObjectManager\Config
{
    /**
     * @var \Magento\Framework\ObjectManager\Config
     */
    private $subjectConfig;

    public function __construct(\Magento\Framework\ObjectManager\Config $config)
    {
        $this->subjectConfig = $config;
    }

    /**
     * Set class relations
     *
     * @param Relations $relations
     *
     * @return void
     */
    public function setRelations(Relations $relations)
    {
        $this->subjectConfig->setRelations($relations);
    }

    /**
     * Set configuration cache instance
     *
     * @param ConfigCache $cache
     *
     * @return void
     */
    public function setCache(ConfigCache $cache)
    {
        $this->subjectConfig->setCache($cache);
    }

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     *
     * @return array
     */
    public function getArguments($type)
    {
        return $this->subjectConfig->getArguments($type);
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     *
     * @return bool
     */
    public function isShared($type)
    {
        return $this->subjectConfig->isShared($type);
    }

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     *
     * @return mixed
     */
    public function getInstanceType($instanceName)
    {
        return $this->subjectConfig->getInstanceType($instanceName);
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     *
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        return $this->subjectConfig->getPreference($type);
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     *
     * @return void
     */
    public function extend(array $configuration)
    {
        $this->subjectConfig->extend($configuration);
    }

    /**
     * Returns list of virtual types
     *
     * @return array
     */
    public function getVirtualTypes()
    {
        return $this->subjectConfig->getVirtualTypes();
    }
}
