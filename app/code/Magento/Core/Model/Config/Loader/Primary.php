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
        // 1. app/etc/*.xml (except local config)
        foreach (scandir($etcDir) as $filename) {
            if ('.' == $filename || '..' == $filename || '.xml' != substr($filename, -4)
                || Magento_Core_Model_Config_Loader_Local::LOCAL_CONFIG_FILE == $filename
            ) {
                continue;
            }
            $baseConfigFile = $etcDir . DIRECTORY_SEPARATOR . $filename;
            $baseConfig = new Magento_Core_Model_Config_Base('<config/>');
            $baseConfig->loadFile($baseConfigFile);
            $config->extend($baseConfig);
        }
        // 2. local configuration
        $this->_localLoader->load($config);
    }
}
