<?php
/**
 * Locale configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Locales implements Magento_Core_Model_Config_LoaderInterface
{
    /**
     * Base dirs model
     *
     * @var Magento_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Element prototype factory
     *
     * @var Magento_Core_Model_Config_BaseFactory
     */
    protected $_factory;

    /**
     * @param Magento_Core_Model_Dir $dirs
     * @param Magento_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(Magento_Core_Model_Dir $dirs, Magento_Core_Model_Config_BaseFactory $factory)
    {
        $this->_dirs = $dirs;
        $this->_factory = $factory;
    }

    /**
     * Populate configuration object
     * Load locale configuration from locale configuration files
     *
     * @param Magento_Core_Model_Config_Base $config
     */
    public function load(Magento_Core_Model_Config_Base $config)
    {
        $localeDir = $this->_dirs->getDir(Magento_Core_Model_Dir::LOCALE);
        $files = glob($localeDir . DIRECTORY_SEPARATOR . '*' . DIRECTORY_SEPARATOR . 'config.xml');

        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $merge = $this->_factory->create();
                $merge->loadFile($file);
                $config->extend($merge);
            }
        }
    }
}
