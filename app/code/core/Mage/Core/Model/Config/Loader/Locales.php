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
    public function __construct(Mage_Core_Model_Dir $dirs)
    {

    }

    /**
     * Load locale configuration from locale configuration files
     *
     * @return Mage_Core_Model_Config
     */
    protected function _loadLocales()
    {
        $localeDir = $this->_dirs->getDir(Mage_Core_Model_Dir::LOCALE);
        $files = glob($localeDir . DS . '*' . DS . 'config.xml');

        if (is_array($files) && !empty($files)) {
            foreach ($files as $file) {
                $merge = clone $this->_prototype;
                $merge->loadFile($file);
                $this->_container->extend($merge);
            }
        }
        return $this;
    }

    /**
     * Populate configuration object
     *
     * @param Mage_Core_Model_Config_Base $config
     */
    public function load(Mage_Core_Model_Config_Base $config) //$config is not empty
    {
        // TODO: Implement load() method.
        $config->extend($data);
    }


}
