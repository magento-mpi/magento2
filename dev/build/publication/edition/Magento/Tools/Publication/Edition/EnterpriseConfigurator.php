<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\Tools\Publication\Edition;

/**
 * Enterprise edition configurator
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
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $_filesystemDriver;

    /**
     * @param string $basePath
     * @param \Magento\Framework\Filesystem\Driver\File $filesystemDriver
     */
    public function __construct($basePath, \Magento\Framework\Filesystem\Driver\File $filesystemDriver)
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

        //set downloader chanel
        $configFile = $this->_basePath . '/downloader/config.ini';
        $content = $this->_filesystemDriver->fileGetContents($configFile);
        $content = str_replace('community', 'enterprise', $content);
        $this->_filesystemDriver->filePutContents($configFile, $content);
    }
}
