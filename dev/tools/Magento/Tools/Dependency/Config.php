<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

/**
 * Config
 */
class Config
{
    /**
     * Modules
     *
     * @var \Magento\Tools\Dependency\Module[]
     */
    private $modules;

    /**
     * Config construct
     *
     * @param \Magento\Tools\Dependency\Module[] $modules
     */
    public function __construct(array $modules = array())
    {
        $this->modules = $modules;
    }

    /**
     * @return \Magento\Tools\Dependency\Module[]
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Get total dependencies count
     *
     * @return int
     */
    public function getDependenciesCount()
    {
        return $this->getHardDependenciesCount() + $this->getSoftDependenciesCount();
    }

    /**
     * Get hard dependencies count
     *
     * @return int
     */
    public function getHardDependenciesCount()
    {
        $dependenciesCount = 0;
        foreach ($this->getModules() as $module) {
            $dependenciesCount += $module->getHardDependenciesCount();
        }
        return $dependenciesCount;
    }

    /**
     * Get soft dependencies count
     *
     * @return int
     */
    public function getSoftDependenciesCount()
    {
        $dependenciesCount = 0;
        foreach ($this->getModules() as $module) {
            $dependenciesCount += $module->getSoftDependenciesCount();
        }
        return $dependenciesCount;
    }
}
