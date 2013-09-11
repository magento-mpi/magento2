<?php
/**
 * Local Application configuration loader (app/etc/local.xml)
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Loader;

class Local implements \Magento\Core\Model\Config\LoaderInterface
{
    /**
     * Local configuration file
     */
    const LOCAL_CONFIG_FILE = 'local.xml';

    /**
     * \Directory registry
     *
     * @var \Magento\Core\Model\Dir
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
     * Populate configuration object
     *
     * @param \Magento\Core\Model\Config\Base $config
     */
    public function load(\Magento\Core\Model\Config\Base $config)
    {
        $localConfigParts = array();

        $localConfigFile = $this->_dir . DIRECTORY_SEPARATOR . self::LOCAL_CONFIG_FILE;
        if (file_exists($localConfigFile)) {
            // 1. app/etc/local.xml
            $localConfig = new \Magento\Core\Model\Config\Base('<config/>');
            $localConfig->loadFile($localConfigFile);
            $localConfigParts[] = $localConfig;

            // 2. app/etc/<dir>/<file>.xml
            if (preg_match('/^[a-z\d_-]+(\/|\\\)+[a-z\d_-]+\.xml$/', $this->_customFile)) {
                $localConfigExtraFile = $this->_dir . DIRECTORY_SEPARATOR . $this->_customFile;
                $localConfig = new \Magento\Core\Model\Config\Base('<config/>');
                $localConfig->loadFile($localConfigExtraFile);
                $localConfigParts[] = $localConfig;
            }
        }

        // 3. extra local configuration string
        if ($this->_customConfig) {
            $localConfig = new \Magento\Core\Model\Config\Base('<config/>');
            $localConfig->loadString($this->_customConfig);
            $localConfigParts[] = $localConfig;
        }

        if ($localConfigParts) {
            foreach ($localConfigParts as $oneConfigPart) {
                $config->extend($oneConfigPart);
            }
        }
    }
}
