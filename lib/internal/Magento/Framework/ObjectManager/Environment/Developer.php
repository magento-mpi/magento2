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
use Magento\Framework\ObjectManager\Factory\Dynamic\Developer as FactoryDeveloper;

class Developer implements EnvironmentInterface
{
    /**
     * Mode name
     */
    const MODE = 'developer';

    /**
     * @var \Magento\Framework\Interception\ObjectManager\Config
     */
    private $config;

    /**
     * @var \Magento\Framework\ObjectManager\Factory
     */
    private $factory;

    /**
     * @var EnvironmentFactory
     */
    private $envFactory;

    /**
     * @param EnvironmentFactory $envFactory
     */
    public function __construct(EnvironmentFactory $envFactory)
    {
        $this->envFactory = $envFactory;
    }

    /**
     * @return \Magento\Framework\Interception\ObjectManager\Config
     */
    public function getDiConfig()
    {
        if (!$this->config) {
            $this->config = new \Magento\Framework\Interception\ObjectManager\Config(
                new \Magento\Framework\ObjectManager\Config\Config(
                    $this->envFactory->getRelations(),
                    $this->envFactory->getDefinitions()
                )
            );
        }

        return $this->config;
    }

    /**
     * @return FactoryDecorator | FactoryDeveloper
     */
    public function getObjectManagerFactory()
    {
        $factoryClass = $this->config->getPreference('\Magento\Framework\ObjectManager\Factory\Dynamic\Developer');

        $this->factory = new $factoryClass(
            $this->config,
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
    public static function getMode()
    {
        return self::MODE;
    }

    /**
     * @return null
     */
    public function getObjectManagerConfigLoader()
    {
        return null;
    }
}
