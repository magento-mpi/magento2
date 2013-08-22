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
     * Directory registry
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Local config loader
     *
     * @var Magento_Core_Model_Config_Loader_Local
     */
    protected $_localLoader;

    /**
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param Magento_Core_Model_Config_Loader_Local $localLoader
     * @param $dir
     */
    public function __construct(Magento_Core_Model_Config_Loader_Local $localLoader, $dir)
    {
        $this->_localLoader = $localLoader;
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
        // 2. local configuration
        $this->_localLoader->load($config);
    }
}
