<?php
/**
 * Primary configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Primary
{
    /**
     * Directory registry
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Local config loader
     *
     * @var Mage_Core_Model_Config_Loader_Local
     */
    protected $_localLoader;

    /**
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_prototypeFactory;

    /**
     * @param Mage_Core_Model_Config_BaseFactory $prototypeFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Config_Loader_Local $localLoader
     */
    public function __construct(
        Mage_Core_Model_Config_BaseFactory $prototypeFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Config_Loader_Local $localLoader
    ) {
        $this->_dirs = $dirs;
        $this->_localLoader = $localLoader;
        $this->_prototypeFactory = $prototypeFactory;
    }

    /**
     * Load primary configuration
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        if (!$config->getNode()) {
            $config->loadString('<config/>');
        }
        // 1. app/etc/*.xml (except local config)
        foreach (scandir($etcDir) as $filename) {
            if ('.' == $filename || '..' == $filename || '.xml' != substr($filename, -4)
                || Mage_Core_Model_Config_Loader_Local::LOCAL_CONFIG_FILE == $filename
            ) {
                continue;
            }
            $baseConfigFile = $etcDir . DIRECTORY_SEPARATOR . $filename;
            $baseConfig = $this->_prototypeFactory->create('<config/>');
            $baseConfig->loadFile($baseConfigFile);
            $config->extend($baseConfig);
        }
        // 2. local configuration
        $this->_localLoader->load($config);
    }
}
