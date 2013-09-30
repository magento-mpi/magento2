<?php
/**
 * Primary configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Primary implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * Config Directory
     *
     * @var string
     */
    protected $_dir;

    /**
     * Config factory
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param string $dir
     */
    public function __construct($dir)
    {
        $this->_dir = $dir;
    }

    /**
     * Load primary configuration
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function load(Magento_Core_Model_Config_Base $config)
    {
        $etcDir = $this->_dir;
        if (!$config->getNode()) {
            $config->loadString('<config/>');
        }
        $files = glob($etcDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.xml');
        array_unshift($files, $etcDir . DIRECTORY_SEPARATOR . 'config.xml');
        // 1. app/etc/*.xml (except local config)
        foreach ($files as $filename) {
            $baseConfig = new Magento_Core_Model_Config_Base('<config/>');
            $baseConfig->loadFile($filename);
            $config->extend($baseConfig);
        }
    }
}
