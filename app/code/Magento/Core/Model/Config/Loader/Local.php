<?php
/**
 * Local Application configuration loader (app/etc/local.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Core_Model_Config_Loader_Local
{
    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * Directory registry
     *
     * @var string
     */
    protected $_dirs;

    /**
     * Custom config file
     *
     * @var string
     */
    protected $_customFile;

    /**
     * Custom configuration string
     *
     * @var string
     */
    protected $_customConfig;

    /**
     * Configuration identifier attributes
     *
     * @var array
     */
    protected $_idAttributes = array('/config/resource' => 'name', '/config/connection' => 'name');

    /**
     * @param string $configDirectory
     * @param string $customConfig
     * @param string $customFile
     */
    public function __construct($configDirectory, $customConfig = null, $customFile = null)
    {
        $this->_dir = $configDirectory;
        $this->_customFile = $customFile;
        $this->_customConfig = $customConfig;
    }

    /**
     * Load configuration
     *
     * @return array
     */
    public function load()
    {
        $localConfig = new Magento_Config_Dom('<config/>', $this->_idAttributes);

        $localConfigFile = $this->_dir . DIRECTORY_SEPARATOR . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig->merge(file_get_contents($localConfigFile));

            // 2. app/etc/<dir>/<file>.xml
            if (preg_match('/^[a-z\d_-]+(\/|\\\)+[a-z\d_-]+\.xml$/', $this->_customFile)) {
                $localConfigExtraFile = $this->_dir . DIRECTORY_SEPARATOR . $this->_customFile;
                $localConfig->merge(file_get_contents($localConfigExtraFile));
            }
        }

        // 3. extra local configuration string
        if ($this->_customConfig) {
            $localConfig->merge($this->_customConfig);
        }
        $converter = new Magento_Config_Converter_Dom_Flat($this->_idAttributes);

        $result = $converter->convert($localConfig->getDom());
        return !empty($result['config']) ? $result['config'] : array();
    }
}
