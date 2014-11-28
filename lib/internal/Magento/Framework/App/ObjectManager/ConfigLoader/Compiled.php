<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\ObjectManager\ConfigLoader;

class Compiled extends \Magento\Framework\App\ObjectManager\ConfigLoader
{
    /**
     * Global config
     *
     * @var array
     */
    private $globalConfig = [];

    /**
     * @param array $globalConfig
     */
    public function __construct($globalConfig)
    {
        $this->globalConfig = $globalConfig;
    }

    /**
     * Load modules DI configuration
     *
     * @param string $area
     * @return array|mixed
     */
    public function load($area)
    {
        if ($area == 'global') {
            return $this->globalConfig;
        }
        return \unserialize(\file_get_contents(BP . '/var/di/' . $area . '.ser'));
    }
}
