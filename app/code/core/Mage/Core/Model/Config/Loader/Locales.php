<?php
/**
 * Locale configuration loader
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Locales implements Mage_Core_Model_Config_LoaderInterface
{
    /**
     * Base dirs model
     *
     * @var Mage_Core_Model_Dir
     */
    protected $_dirs;

    /**
     * Element prototype factory
     *
     * @var Mage_Core_Model_Config_BaseFactory
     */
    protected $_factory;

    /**
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Config_BaseFactory $factory
     */
    public function __construct(Mage_Core_Model_Dir $dirs, Mage_Core_Model_Config_BaseFactory $factory)
    {
        $this->_dirs = $dirs;
    }

    /**
     * Populate configuration object
     * Load locale configuration from locale configuration files
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config)
    {
        $localeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::LOCALE);
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
