<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

/**
 * Module
 */
class Module
{
    /**
     * Module name
     *
     * @var string
     */
    private $name;

    /**
     * Module dependencies
     *
     * @var \Magento\Tools\Dependency\Dependency[]
     */
    private $dependencies;

    /**
     * Module construct
     *
     * @param array $name
     * @param \Magento\Tools\Dependency\Dependency[] $dependencies
     */
    public function __construct($name, array $dependencies = array())
    {
        $this->name = $name;
        $this->dependencies = $dependencies;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \Magento\Tools\Dependency\Dependency[]
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Check if is module has dependencies
     *
     * @return bool
     */
    public function hasDependencies()
    {
        return (bool)$this->dependencies;
    }

    /**
     * Get total dependencies count
     *
     * @return int
     */
    public function getDependenciesCount()
    {
        return count($this->dependencies);
    }

    /**
     * Get hard dependencies count
     *
     * @return int
     */
    public function getHardDependenciesCount()
    {
        $dependenciesCount = 0;
        foreach ($this->getDependencies() as $dependency) {
            if ($dependency->isHard()) {
                $dependenciesCount++;
            }
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
        foreach ($this->getDependencies() as $dependency) {
            if (!$dependency->isHard()) {
                $dependenciesCount++;
            }
        }
        return $dependenciesCount;
    }
}
