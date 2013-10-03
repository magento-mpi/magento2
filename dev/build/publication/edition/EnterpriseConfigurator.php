<?php
/**
 * Enterprise edition configurator
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class EnterpriseConfigurator implements ConfiguratorInterface
{
    /**
     * Base instance path
     *
     * @var string
     */
    protected $_basePath;

    /**
     * @var \Magento\Io\File
     */
    protected $_filesystem;

    /**
     * @param $basePath
     * @param \Magento\Io\File $filesystem
     */
    public function __construct($basePath, \Magento\Io\File $filesystem)
    {
        $this->_basePath = $basePath;
        $this->_filesystem = $filesystem;
    }

    /**
     * Configure Magento instance
     *
     * @return void
     */
    public function configure()
    {
        $enablerPath = $this->_basePath
            . DIRECTORY_SEPARATOR . 'app'
            . DIRECTORY_SEPARATOR . 'etc'
            . DIRECTORY_SEPARATOR;

        //enable enterprise edition modules
        $this->_filesystem->cp(
            $enablerPath . 'enterprise' . DIRECTORY_SEPARATOR . 'module.xml.dist',
            $enablerPath . 'enterprise' . DIRECTORY_SEPARATOR . 'module.xml'
        );

        //set edition constant
        $appFile = $this->_basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'code'
            . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . 'Model' . DIRECTORY_SEPARATOR . 'App.php';
        $content = $this->_filesystem->read($appFile);
        $content = str_replace('self::EDITION_COMMUNITY', 'self::EDITION_ENTERPRISE', $content);
        $this->_filesystem->write($appFile, $content);

        //set downloader chanel
        $configFile = $this->_basePath . DIRECTORY_SEPARATOR . 'downloader' . DIRECTORY_SEPARATOR . 'config.ini';
        $content = $this->_filesystem->read($configFile);
        $content = str_replace('community', 'enterprise', $content);
        $this->_filesystem->write($configFile, $content);
    }
}
