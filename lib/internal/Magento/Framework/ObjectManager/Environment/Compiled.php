<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ObjectManager\Environment;

use Magento\Framework\ObjectManager\EnvironmentInterface;

class Compiled extends AbstractEnvironment implements EnvironmentInterface
{
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

    /**#@+
     * Mode name
     */
    const MODE = 'compiled';
    protected $mode = self::MODE;
    /**#@- */

    /**
     * @var string
     */
    protected $configPreference = '\Magento\Framework\ObjectManager\Factory\Compiled';

    /**
     * @return \Magento\Framework\Interception\ObjectManager\Config
     */
    public function getDiConfig()
    {
        if (!$this->config) {
            $this->compiledConfig = $this->getConfigData();
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
     * @return \Magento\Framework\App\ObjectManager\ConfigLoader\Compiled
     */
    public function getObjectManagerConfigLoader()
    {
        return new \Magento\Framework\App\ObjectManager\ConfigLoader\Compiled();
    }
}
