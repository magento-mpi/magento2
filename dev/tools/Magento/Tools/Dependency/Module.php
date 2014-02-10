<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

/**
 * Generator Interface
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
}
