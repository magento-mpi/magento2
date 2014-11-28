<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ObjectManager\Environment;

use Magento\Framework\ObjectManager\EnvironmentFactory;
use Magento\Framework\ObjectManager\EnvironmentInterface;
use Magento\Framework\ObjectManager\Profiler\FactoryDecorator;
use Magento\Framework\ObjectManager\Factory\Compiled as FactoryCompiled;

abstract class AbstractEnvironment implements EnvironmentInterface
{
    /**
     * @var \Magento\Framework\Interception\ObjectManager\Config
     */
    protected $config;

    /**
     * Mode name
     */
    protected $mode = 'developer';

    /**
     * @var string
     */
    protected $configPreference = '\Magento\Framework\ObjectManager\Factory\Dynamic\Developer';

    /**
     * @var \Magento\Framework\ObjectManager\FactoryInterface
     */
    private $factory;

    /**
     * @var EnvironmentFactory
     */
    protected $envFactory;

    /**
     * @param EnvironmentFactory $envFactory
     */
    public function __construct(EnvironmentFactory $envFactory)
    {
        $this->envFactory = $envFactory;
    }

    /**
     * @return FactoryDecorator | FactoryCompiled
     */
    public function getObjectManagerFactory()
    {
        $this->factory = new $this->configPreference(
            $this->getDiConfig(),
            null,
            $this->envFactory->getDefinitions(),
            $this->envFactory->getAppArguments()->get()
        );

        if ($this->envFactory->getAppArguments()->get('MAGE_PROFILER') == 2) {
            $this->factory = new FactoryDecorator(
                $this->factory,
                \Magento\Framework\ObjectManager\Profiler\Log::getInstance()
            );
        }

        return $this->factory;
    }

    /**
     * Return name of running mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->mode;
    }
}
