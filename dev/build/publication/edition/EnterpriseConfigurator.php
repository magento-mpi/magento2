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
     * @var \Magento\Filesystem\Driver\File
     */
    protected $_filesystemDriver;

    /**
     * @param $basePath
     * @param \Magento\Filesystem\Driver\File $filesystemDriver
     */
    public function __construct($basePath, \Magento\Filesystem\Driver\File $filesystemDriver)
    {
        $this->_basePath = $basePath;
        $this->_filesystemDriver = $filesystemDriver;
    }

    /**
     * Configure Magento instance
     *
     * @return void
     */
    public function configure()
    {
        $enablerPath = $this->_basePath . '/app/etc/';
        //enable enterprise edition modules
        $this->_filesystemDriver->copy(
            $enablerPath . 'enterprise/module.xml.dist',
            $enablerPath . 'enterprise/module.xml'
        );

        //set edition constant
        $appFile = $this->_basePath . '/app/code/Magento/Core/Model/App.php';
        $content = $this->_filesystemDriver->fileGetContents($appFile);
        $content = str_replace('self::EDITION_COMMUNITY', 'self::EDITION_ENTERPRISE', $content);
        $this->_filesystemDriver->filePutContents($appFile, $content);

        //set downloader chanel
        $configFile = $this->_basePath . '/downloader/config.ini';
        $content = $this->_filesystemDriver->fileGetContents($configFile);
        $content = str_replace('community', 'enterprise', $content);
        $this->_filesystemDriver->filePutContents($configFile, $content);
    }
}
