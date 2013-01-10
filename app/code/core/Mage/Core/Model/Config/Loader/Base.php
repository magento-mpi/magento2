<?php
/**
 * Base Application configuration loader (app/etc/*.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Config_Loader_Base
{
    /**
     * Load base configuration
     *
     * @return Mage_Core_Model_Config_Storage
     */
    protected function load(Mage_Core_Model_Config_Base $config)
    {
        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        if (!$this->_container->getNode()) {
            $this->_container->loadString('<config/>');
        }
        // 1. app/etc/*.xml (except local config)
        foreach (scandir($etcDir) as $filename) {
            if ('.' == $filename || '..' == $filename || '.xml' != substr($filename, -4)
                || self::LOCAL_CONFIG_FILE == $filename
            ) {
                continue;
            }
            $baseConfigFile = $etcDir . DIRECTORY_SEPARATOR . $filename;
            $baseConfig = clone $this->_prototype;
            $baseConfig->loadFile($baseConfigFile);
            $this->_container->extend($baseConfig);
        }
        // 2. local configuration
        $this->_loadLocalConfig();
    }

    /**
     * Load local configuration (part of the base configuration)
     */
    protected function _loadLocalConfig()
    {
        $etcDir = $this->_dirs->getDir(Mage_Core_Model_Dir::CONFIG);
        $localConfigParts = array();

        $localConfigFile = $etcDir . DIRECTORY_SEPARATOR . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig = clone $this->_prototype;
            $localConfig->loadFile($localConfigFile);
            $localConfigParts[] = $localConfig;

            // 2. app/etc/<dir>/<file>.xml
            $localConfigExtraFile = $this->_extraFile;
            if (preg_match('/^[a-z\d_-]+\/[a-z\d_-]+\.xml$/', $localConfigExtraFile)) {
                $localConfigExtraFile = $etcDir . DIRECTORY_SEPARATOR . $localConfigExtraFile;
                $localConfig = clone $this->_prototype;
                $localConfig->loadFile($localConfigExtraFile);
                $localConfigParts[] = $localConfig;
            }
        }

        // 3. extra local configuration string
        $localConfigExtraData = $this->_extraData;
        if ($localConfigExtraData) {
            $localConfig = clone $this->_prototype;
            $localConfig->loadString($localConfigExtraData);
            $localConfigParts[] = $localConfig;
        }

        if ($localConfigParts) {
            foreach ($localConfigParts as $oneConfigPart) {
                $this->_container->extend($oneConfigPart);
            }
            $this->_isLocalConfigLoaded = true;
        }
    }
}
