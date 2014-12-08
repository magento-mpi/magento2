<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Dependency\Report\Data\Config;

use Magento\Tools\Dependency\Report\Data\ConfigInterface;

/**
 * Config
 */
abstract class AbstractConfig implements ConfigInterface
{
    /**
     * Modules
     *
     * @var array
     */
    private $modules;

    /**
     * Config construct
     *
     * @param array $modules
     */
    public function __construct(array $modules = [])
    {
        $this->modules = $modules;
    }

    /**
     * {@inheritdoc}
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function getDependenciesCount();
}
