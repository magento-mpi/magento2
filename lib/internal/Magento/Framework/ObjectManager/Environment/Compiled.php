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

class Compiled implements EnvironmentInterface
{
    /**
     * Mode name
     */
    const MODE = 'compiled';

    /**
     * File name with compiled data
     */
    const FILE_NAME = 'global.ser';

    /**
     * Relative path to file with compiled data
     */
    const RELATIVE_FILE_PATH = '/var/di/';

    /**
     * Unserialized config data
     *
     * @var array
     */
    private $compiledConfig = [];

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
            $this->compiledConfig =  $this->getConfigData();
            $this->config = new \Magento\Framework\Interception\ObjectManager\Config(
                new \Magento\Framework\ObjectManager\Config\Compiled($this->compiledConfig)
            );
        }

        return $this->config;
    }

    /**
     * Return unserialized config data
     * @return mixed
     */
    private function getConfigData()
    {
        return \unserialize(\file_get_contents(self::getFilePath()));
    }

    /**
     * @return string
     */
    public static function getFilePath()
    {
        return BP . self::RELATIVE_FILE_PATH . self::FILE_NAME;
    }

    /**
     * @return FactoryDecorator | FactoryCompiled
     */
    public function getObjectManagerFactory()
    {
        $factoryClass = $this->config->getPreference('\Magento\Framework\ObjectManager\Factory\Compiled');

        $this->factory = new $factoryClass(
            $this->config,
            null,
            $this->envFactory->getDefinitions(),
            $this->envFactory->getAppArguments()->get()
        );

        if ($this->envFactory->getAppArguments()->get('MAGE_PROFILER') == 2) {
            $this->factory = new \Magento\Framework\ObjectManager\Profiler\FactoryDecorator(
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
     * @return \Magento\Framework\App\ObjectManager\ConfigLoader\Compiled
     */
    public function getObjectManagerConfigLoader()
    {
        return new \Magento\Framework\App\ObjectManager\ConfigLoader\Compiled();
    }
}
