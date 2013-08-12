<?php
/**
 * Saas edition configurator
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
class SaasConfigurator implements ConfiguratorInterface
{
    /**
     * Base instance path
     *
     * @var string
     */
    protected $_basePath;

    /**
     * @var Magento_Io_File
     */
    protected $_filesystem;

    /**
     * @param $basePath
     * @param Magento_Io_File $filesystem
     */
    public function __construct($basePath, Magento_Io_File $filesystem)
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

        //enable saas edition modules
        $this->_filesystem->cp(
            $enablerPath . 'saas' . DIRECTORY_SEPARATOR . 'module.xml.dist',
            $enablerPath . 'saas' . DIRECTORY_SEPARATOR . 'module.xml'
        );

        //set edition constant
        $mageFile = $this->_basePath . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Mage.php';
        $content = $this->_filesystem->read($mageFile);
        $content = str_replace('self::EDITION_COMMUNITY', 'self::EDITION_GO', $content);
        $this->_filesystem->write($mageFile, $content);
    }
}
